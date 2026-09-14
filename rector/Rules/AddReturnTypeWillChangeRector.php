<?php

declare(strict_types=1);

namespace App\Rector\Rules;

use PhpParser\Node;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\UnionType;
use PHPStan\Type\ObjectType;
use Rector\Rector\AbstractRector;

use function in_array;
use function strtolower;

/**
 * PHP 8.1 gave the methods of some core interfaces a tentative return type.
 * A declaration that does not repeat a compatible native return type triggers
 * a deprecation notice unless it carries `#[\ReturnTypeWillChange]`.
 *
 * The downgrade strips exactly those return types PHP 7.4 cannot express
 * (`mixed`, `never`, `null`, `static`, unions), which leaves such declarations
 * untyped. This rule marks them so the downgraded release stays silent on
 * PHP 8.1+, and keeps the attribute out of the PHP 8.4 sources, where the
 * native types already match and the attribute would be pure noise.
 */
final class AddReturnTypeWillChangeRector extends AbstractRector
{
    /**
     * Interface => methods carrying a tentative return type since PHP 8.1.
     *
     * @var array<class-string, list<string>>
     */
    private const TENTATIVE_RETURN_TYPES = [
        'ArrayAccess' => ['offsetExists', 'offsetGet', 'offsetSet', 'offsetUnset'],
        'Countable' => ['count'],
        'IteratorAggregate' => ['getIterator'],
        'JsonSerializable' => ['jsonSerialize'],
        'Iterator' => ['current', 'key', 'next', 'rewind', 'valid'],
        'Serializable' => ['serialize', 'unserialize'],
    ];

    /**
     * Return types that `->withDowngradeSets(php74: true)` removes entirely,
     * leaving the declaration without a native return type.
     *
     * @var list<string>
     */
    private const UNSUPPORTED_RETURN_TYPES = ['mixed', 'never', 'null'];

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class, Interface_::class, Trait_::class];
    }

    public function refactor(Node $node): ?Node
    {
        $hasChanged = false;

        foreach (self::TENTATIVE_RETURN_TYPES as $interface => $methodNames) {
            if (!$this->isObjectType($node, new ObjectType($interface))) {
                continue;
            }

            foreach ($methodNames as $methodName) {
                $classMethod = $node->getMethod($methodName);
                if (!$classMethod instanceof ClassMethod) {
                    continue;
                }

                if ($this->refactorClassMethod($classMethod)) {
                    $hasChanged = true;
                }
            }
        }

        return $hasChanged ? $node : null;
    }

    private function hasAttribute(ClassMethod $classMethod): bool
    {
        foreach ($classMethod->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                if (strtolower($attribute->name->toString()) === 'returntypewillchange') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * True when the downgrade leaves (or already left) the method without a
     * native return type.
     */
    private function losesReturnType(ClassMethod $classMethod): bool
    {
        $returnType = $classMethod->returnType;

        // Already stripped by a downgrade rule that ran before this one.
        if ($returnType === null) {
            return true;
        }

        // Union and intersection types do not exist in PHP 7.4.
        if ($returnType instanceof UnionType || $returnType instanceof IntersectionType) {
            return true;
        }

        if ($returnType instanceof Identifier) {
            return in_array(strtolower($returnType->toString()), self::UNSUPPORTED_RETURN_TYPES, true);
        }

        // `static` as a return type is PHP 8.0+.
        return $returnType instanceof Name && strtolower($returnType->toString()) === 'static';
    }

    private function refactorClassMethod(ClassMethod $classMethod): bool
    {
        if (!$this->losesReturnType($classMethod)) {
            return false;
        }

        if ($this->hasAttribute($classMethod)) {
            return false;
        }

        $classMethod->attrGroups[] = new AttributeGroup([
            new Attribute(new FullyQualified('ReturnTypeWillChange')),
        ]);

        return true;
    }
}
