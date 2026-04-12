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
        ];
    }

    public function refactor(Node $node): ?Node
    {
        $addedParamDocs = [];

        foreach ($node->getParams() as $param) {
            $literalType = $this->matchStandaloneLiteralType($param);
            if ($literalType === null) {
                continue;
            }

            $paramName = $this->getName($param->var);
            if (!is_string($paramName)) {
                continue;
            }

            $param->type = null;
            $addedParamDocs[] = sprintf('@param %s $%s', $literalType, $paramName);
        }

        if ($addedParamDocs === []) {
            return null;
        }

        $this->appendParamDocs($node, $addedParamDocs);

        return $node;
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

    private function matchStandaloneLiteralType(Param $param): ?string
    {
        if (!$param->type instanceof Identifier) {
            return null;
        }

        $typeName = $this->getName($param->type);
        if (!is_string($typeName)) {
            return null;
        }

        return match ($typeName) {
            'null', 'true', 'false' => $typeName,
            default => null,
        };
    }
}
