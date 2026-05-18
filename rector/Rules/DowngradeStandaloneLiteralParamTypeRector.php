<?php

declare(strict_types=1);

namespace App\Rector\Rules;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Identifier;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Property;
use Rector\Rector\AbstractRector;

use function is_string;
use function sprintf;

final class DowngradeStandaloneLiteralParamTypeRector extends AbstractRector
{
    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [
            ClassMethod::class,
            Function_::class,
            Closure::class,
            ArrowFunction::class,
            Property::class,
        ];
    }

    public function refactor(Node $node): ?Node
    {
        if ($node instanceof Property) {
            return $this->refactorProperty($node);
        }

        return $this->refactorFunctionLike($node);
    }

    /**
     * @param list<string> $addedParamDocs
     */
    private function appendParamDocs(Node $node, array $addedParamDocs): void
    {
        $existingDoc = $node->getDocComment()?->getText();

        if ($existingDoc === null) {
            $lines = ['/**'];
            foreach ($addedParamDocs as $addedParamDoc) {
                $lines[] = ' * ' . $addedParamDoc;
            }
            $lines[] = ' */';

            $node->setDocComment(new Doc(implode("\n", $lines)));

            return;
        }

        foreach ($addedParamDocs as $addedParamDoc) {
            if (str_contains($existingDoc, $addedParamDoc)) {
                continue;
            }

            // insert before closing */
            $existingDoc = preg_replace(
                '#\n\s*\*/$#',
                "\n * {$addedParamDoc}\n */",
                $existingDoc
            ) ?? $existingDoc;
        }

        $node->setDocComment(new Doc($existingDoc));
    }

    private function matchStandaloneLiteralType(?Node $type): ?string
    {
        if (!$type instanceof Identifier) {
            return null;
        }

        $typeName = $this->getName($type);
        if (!is_string($typeName)) {
            return null;
        }

        return match ($typeName) {
            'null', 'true', 'false' => $typeName,
            default => null,
        };
    }

    private function refactorFunctionLike(Node $node): ?Node
    {
        $hasChanged = false;
        $addedParamDocs = [];

        // Refactor Parameters
        foreach ($node->getParams() as $param) {
            $literalType = $this->matchStandaloneLiteralType($param->type);
            if ($literalType === null) {
                continue;
            }

            $paramName = $this->getName($param->var);
            if (!is_string($paramName)) {
                continue;
            }

            $param->type = null;
            $addedParamDocs[] = sprintf('@param %s $%s', $literalType, $paramName);
            $hasChanged = true;
        }

        if ($addedParamDocs !== []) {
            $this->appendParamDocs($node, $addedParamDocs);
        }

        // Refactor Return Type
        $returnLiteralType = $this->matchStandaloneLiteralType($node->returnType);
        if ($returnLiteralType !== null) {
            $node->returnType = new Identifier('bool');

            $doc = $node->getDocComment()?->getText() ?? '/** */';
            if (!str_contains($doc, '@return')) {
                $newDoc = preg_replace('/(\/\*\*)/', "$1\n     * @return {$returnLiteralType}", $doc);
                $node->setDocComment(new Doc($newDoc));
            }

            $hasChanged = true;
        }

        return $hasChanged ? $node : null;
    }

    private function refactorProperty(Property $node): ?Property
    {
        $literalType = $this->matchStandaloneLiteralType($node->type);
        if ($literalType === null) {
            return null;
        }

        // Downgrade native type to bool
        $node->type = new Identifier('bool');

        // Add PHPDoc @var
        $doc = $node->getDocComment()?->getText() ?? '/** */';
        if (!str_contains($doc, '@var')) {
            $newDoc = preg_replace('/(\/\*\*)/', "$1\n     * @var {$literalType}", $doc);
            $node->setDocComment(new Doc($newDoc));
        }

        return $node;
    }
}
