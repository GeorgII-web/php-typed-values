<?php

//
// namespace PhpTypedValues\Usage\Primitive;
//
// require_once 'vendor/autoload.php';
//
// use const PHP_EOL;
//
// use Exception;
// use PhpTypedValues\Base\Primitive\Bool\BoolTypeInterface;
// use PhpTypedValues\Bool\Alias\Binary;
// use PhpTypedValues\Bool\Alias\BooleanType;
// use PhpTypedValues\Bool\Alias\Flag;
// use PhpTypedValues\Bool\Alias\Logical;
// use PhpTypedValues\Bool\Alias\Toggle;
// use PhpTypedValues\Bool\BoolStandard;
// use PhpTypedValues\Bool\FalseStandard;
// use PhpTypedValues\Bool\TrueStandard;
// use PhpTypedValues\Exception\Undefined\UndefinedTypeException;
// use PhpTypedValues\Undefined\Alias\Undefined;
// use PhpTypedValues\Undefined\UndefinedStandard;
// use Stringable;
//
// use function count;
//
// /**
// * Boolean Types Usage Examples.
// */
// echo PHP_EOL . '===== BOOLEAN TYPES USAGE =====' . PHP_EOL . PHP_EOL;
//
// // ==================== BoolStandard Examples ====================
// echo '=== BoolStandard (Generic Boolean) ===' . PHP_EOL;
//
// echo '1. Basic Construction:' . PHP_EOL;
// echo '   fromString("true"): ' . BoolStandard::fromString('true')->toString() . PHP_EOL;
// echo '   fromInt(1): ' . BoolStandard::fromInt(1)->toString() . PHP_EOL;
// echo '   fromBool(true): ' . BoolStandard::fromBool(true)->toString() . PHP_EOL;
// echo '   fromFloat(1.0): ' . BoolStandard::fromFloat(1.0)->toString() . PHP_EOL;
// echo '   fromFloat(0.0): ' . BoolStandard::fromFloat(0.0)->toString() . PHP_EOL;
//
// echo PHP_EOL . '2. String Conversion:' . PHP_EOL;
// echo '   toString(): ' . BoolStandard::fromString('true')->toString() . PHP_EOL;
// echo '   __toString(): ' . ((string) BoolStandard::fromString('true')) . PHP_EOL;
// echo '   jsonSerialize(): ' . (BoolStandard::fromString('true')->jsonSerialize() ? 'true' : 'false') . PHP_EOL;
//
// echo PHP_EOL . '3. tryFrom Methods:' . PHP_EOL;
// $undefinedType1 = BoolStandard::tryFromInt(2);
// $undefinedType2 = BoolStandard::tryFromString('test');
// echo '   tryFromInt(2) - ' . ($undefinedType1 instanceof Undefined ? 'Undefined' : 'BoolStandard') . PHP_EOL;
// echo '   tryFromString("test") - ' . ($undefinedType2 instanceof Undefined ? 'Undefined' : 'BoolStandard') . PHP_EOL;
//
// // Demonstrate UndefinedTypeException
// try {
//    if ($undefinedType1 instanceof Undefined) {
//        echo '   Accessing value() on Undefined throws: ';
//        $undefinedType1->value();
//    }
// } catch (UndefinedTypeException $e) {
//    echo 'UndefinedTypeException caught' . PHP_EOL;
// }
//
// echo PHP_EOL . '4. Type Conversion Methods:' . PHP_EOL;
// echo '   toBool(): ' . (BoolStandard::fromString('true')->toBool() ? 'true' : 'false') . PHP_EOL;
// echo '   toInt(): ' . BoolStandard::fromBool(true)->toInt() . PHP_EOL;
// $floatValue = BoolStandard::fromBool(true)->toFloat();
// echo '   toFloat(): ' . ((string) $floatValue) . PHP_EOL;
//
// echo PHP_EOL . '5. Type Checking:' . PHP_EOL;
// echo '   isTypeOf(BoolStandard::class): ' . (BoolStandard::fromBool(true)->isTypeOf(BoolStandard::class) ? 'true' : 'false') . PHP_EOL;
// echo '   isTypeOf("InvalidClass"): ' . (BoolStandard::fromBool(true)->isTypeOf('InvalidClass') ? 'true' : 'false') . PHP_EOL;
// echo '   isTypeOf multiple - one matches: ' . (BoolStandard::fromBool(true)->isTypeOf('InvalidClass', BoolStandard::class) ? 'true' : 'false') . PHP_EOL;
// echo '   isEmpty(): ' . (BoolStandard::fromBool(true)->isEmpty() ? 'true' : 'false') . PHP_EOL;
// echo '   isUndefined(): ' . (BoolStandard::fromBool(true)->isUndefined() ? 'true' : 'false') . PHP_EOL;
//
// echo PHP_EOL . '6. tryFromBool with custom default:' . PHP_EOL;
// $tryFromBoolResult = BoolStandard::tryFromBool(true, UndefinedStandard::create());
// echo '   tryFromBool(true, custom): ' . ($tryFromBoolResult instanceof BoolStandard ? 'BoolStandard' : $tryFromBoolResult::class) . PHP_EOL;
//
// echo PHP_EOL . '7. tryFromMixed:' . PHP_EOL;
// echo '   tryFromMixed(true): ' . (BoolStandard::tryFromMixed(true) instanceof BoolStandard ? 'BoolStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromMixed(1): ' . (BoolStandard::tryFromMixed(1) instanceof BoolStandard ? 'BoolStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromMixed("true"): ' . (BoolStandard::tryFromMixed('true') instanceof BoolStandard ? 'BoolStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromMixed("false"): ' . (BoolStandard::tryFromMixed('false') instanceof BoolStandard ? 'BoolStandard' : 'Undefined') . PHP_EOL;
//
// // ==================== TrueStandard Examples ====================
// echo PHP_EOL . '=== TrueStandard (Literal True Only) ===' . PHP_EOL;
//
// echo '1. Construction (only true values):' . PHP_EOL;
// echo '   fromString("true"): ' . TrueStandard::fromString('true')->toString() . PHP_EOL;
// echo '   fromInt(1): ' . TrueStandard::fromInt(1)->toString() . PHP_EOL;
// echo '   fromBool(true): ' . TrueStandard::fromBool(true)->toString() . PHP_EOL;
// echo '   fromFloat(1.0): ' . TrueStandard::fromFloat(1.0)->toString() . PHP_EOL;
//
// echo PHP_EOL . '2. tryFrom Methods:' . PHP_EOL;
// $trueFromString = TrueStandard::tryFromString('true');
// $trueFromInt = TrueStandard::tryFromInt(1);
// $trueFromBool = TrueStandard::tryFromBool(true);
// $trueFromMixed = TrueStandard::tryFromMixed('true');
// echo '   tryFromString("true"): ' . ($trueFromString instanceof TrueStandard ? 'TrueStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromInt(1): ' . ($trueFromInt instanceof TrueStandard ? 'TrueStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromBool(true): ' . ($trueFromBool instanceof TrueStandard ? 'TrueStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromMixed("true"): ' . ($trueFromMixed instanceof TrueStandard ? 'TrueStandard' : 'Undefined') . PHP_EOL;
//
// echo PHP_EOL . '3. Failed Conversions (return Undefined):' . PHP_EOL;
// echo '   tryFromString("false"): ' . (TrueStandard::tryFromString('false') instanceof Undefined ? 'Undefined' : 'TrueStandard') . PHP_EOL;
// echo '   tryFromInt(0): ' . (TrueStandard::tryFromInt(0) instanceof Undefined ? 'Undefined' : 'TrueStandard') . PHP_EOL;
// echo '   tryFromBool(false): ' . (TrueStandard::tryFromBool(false) instanceof Undefined ? 'Undefined' : 'TrueStandard') . PHP_EOL;
//
// // ==================== FalseStandard Examples ====================
// echo PHP_EOL . '=== FalseStandard (Literal False Only) ===' . PHP_EOL;
//
// echo '1. Construction (only false values):' . PHP_EOL;
// echo '   fromString("false"): ' . FalseStandard::fromString('false')->toString() . PHP_EOL;
// echo '   fromInt(0): ' . FalseStandard::fromInt(0)->toString() . PHP_EOL;
// echo '   fromBool(false): ' . FalseStandard::fromBool(false)->toString() . PHP_EOL;
// echo ' ' . FalseStandard::fromFloat(0.0)->toString() . PHP_EOL;
//
// echo PHP_EOL . '2. tryFrom Methods:' . PHP_EOL;
// $falseFromString = FalseStandard::tryFromString('false');
// $falseFromInt = FalseStandard::tryFromInt(0);
// $falseFromBool = FalseStandard::tryFromBool(false);
// $falseFromMixed = FalseStandard::tryFromMixed('false');
// echo '   tryFromString("false"): ' . ($falseFromString instanceof FalseStandard ? 'FalseStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromInt(0): ' . ($falseFromInt instanceof FalseStandard ? 'FalseStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromBool(false): ' . ($falseFromBool instanceof FalseStandard ? 'FalseStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromMixed("false"): ' . ($falseFromMixed instanceof FalseStandard ? 'FalseStandard' : 'Undefined') . PHP_EOL;
//
// echo PHP_EOL . '3. Failed Conversions (return Undefined):' . PHP_EOL;
// echo '   tryFromString("true"): ' . (FalseStandard::tryFromString('true') instanceof Undefined ? 'Undefined' : 'FalseStandard') . PHP_EOL;
// echo '   tryFromInt(1): ' . (FalseStandard::tryFromInt(1) instanceof Undefined ? 'Undefined' : 'FalseStandard') . PHP_EOL;
// echo '   tryFromBool(true): ' . ((FalseStandard::tryFromBool(true) instanceof Undefined) ? 'Undefined' : 'FalseStandard') . PHP_EOL;
//
// // ==================== Alias Classes Examples ====================
// echo PHP_EOL . '=== Boolean Alias Classes ===' . PHP_EOL;
//
// echo '1. BooleanType Alias:' . PHP_EOL;
// echo '   fromBool(true): ' . BooleanType::fromBool(BooleanType::fromBool(true)->value())->toString() . PHP_EOL;
//
// echo PHP_EOL . '2. Binary Alias:' . PHP_EOL;
// echo '   tryFromMixed("true"): ' . Binary::tryFromMixed('true')->toString() . PHP_EOL;
// echo '   tryFromBool(true): ' . (Binary::tryFromBool(true) instanceof Binary ? 'Binary' : 'Undefined') . PHP_EOL;
//
// echo PHP_EOL . '3. Flag Alias:' . PHP_EOL;
// echo '   tryFromMixed("true"): ' . Flag::tryFromMixed('true')->toString() . PHP_EOL;
//
// echo PHP_EOL . '4. Logical Alias:' . PHP_EOL;
// echo '   tryFromMixed("true"): ' . Logical::tryFromMixed('true')->toString() . PHP_EOL;
//
// echo PHP_EOL . '5. Toggle Alias:' . PHP_EOL;
// echo '   tryFromMixed("true"): ' . Toggle::tryFromMixed('true')->toString() . PHP_EOL;
// echo '   fromString("true")->isTypeOf(Toggle::class): '
//    . (Toggle::fromString('true')->isTypeOf(Toggle::class) ? 'Type correct' : 'Invalid type') . PHP_EOL;
//
// echo PHP_EOL . '6. tryFromBool with Aliases:' . PHP_EOL;
// $binaryFromBool = Binary::tryFromBool(true);
// $toggleFromBool = Toggle::tryFromBool(false);
// echo '   Binary::tryFromBool(true): ' . ($binaryFromBool instanceof Binary ? 'Binary' : 'Undefined') . PHP_EOL;
// echo '   Toggle::tryFromBool(false): ' . ($toggleFromBool instanceof Toggle ? 'Toggle' : 'Undefined') . PHP_EOL;
//
// // ==================== Interface Method Coverage ====================
// echo PHP_EOL . '=== BoolTypeInterface Method Coverage ===' . PHP_EOL;
//
// echo '1. value() method usage:' . PHP_EOL;
// $boolForInterface = BoolStandard::fromBool(true);
// $boolValue = testBool($boolForInterface);
// echo '   testBool() returns: ' . ($boolValue ? 'true' : 'false') . PHP_EOL;
//
// echo PHP_EOL . '2. toBool() method usage:' . PHP_EOL;
// $toBoolResult = testToBool(BoolStandard::fromString('true'));
// echo '   BoolStandard: ' . ($toBoolResult ? 'true' : 'false') . PHP_EOL;
// echo '   TrueStandard: ' . (TrueStandard::fromBool(true)->toBool() ? 'true' : 'false') . PHP_EOL;
// echo '   FalseStandard: ' . (FalseStandard::fromBool(false)->toBool() ? 'true' : 'false') . PHP_EOL;
//
// echo PHP_EOL . '3. toFloat() method usage:' . PHP_EOL;
// $floatResult1 = testToFloat(BoolStandard::fromBool(true));
// echo '   BoolStandard(true): ' . ((string) $floatResult1) . PHP_EOL;
// $floatResult2 = testToFloat(BoolStandard::fromBool(false));
// echo '   BoolStandard(false): ' . ((string) $floatResult2) . PHP_EOL;
// $trueStandardFloat = TrueStandard::fromBool(true)->toFloat();
// echo '   TrueStandard: ' . ((string) $trueStandardFloat) . PHP_EOL;
// $falseStandardFloat = FalseStandard::fromBool(false)->toFloat();
// echo '   FalseStandard: ' . ((string) $falseStandardFloat) . PHP_EOL;
//
// echo PHP_EOL . '4. toInt() method usage:' . PHP_EOL;
// echo '   BoolStandard(true): ' . testToInt(BoolStandard::fromBool(true)) . PHP_EOL;
// echo '   BoolStandard(false): ' . testToInt(BoolStandard::fromBool(false)) . PHP_EOL;
// echo '   TrueStandard: ' . TrueStandard::fromBool(true)->toInt() . PHP_EOL;
// echo '   FalseStandard: ' . FalseStandard::fromBool(false)->toInt() . PHP_EOL;
//
// echo PHP_EOL . '5. tryFromFloat examples:' . PHP_EOL;
// $tryFromFloat1 = BoolStandard::tryFromFloat(1.0);
// $tryFromFloat2 = BoolStandard::tryFromFloat(0.0);
// $tryFromFloat3 = BoolStandard::tryFromFloat(2.5);
// echo '   tryFromFloat(1.0): ' . ($tryFromFloat1 instanceof BoolStandard ? $tryFromFloat1->toString() : 'Undefined') . PHP_EOL;
// echo '   tryFromFloat(0.0): ' . ($tryFromFloat2 instanceof BoolStandard ? $tryFromFloat2->toString() : 'Undefined') . PHP_EOL;
// echo '   tryFromFloat(2.5): ' . ($tryFromFloat3 instanceof Undefined ? 'Undefined (invalid float)' : $tryFromFloat3->toString()) . PHP_EOL;
//
// // ==================== Edge Cases ====================
// echo PHP_EOL . '=== Edge Cases ===' . PHP_EOL;
//
// echo '1. Type-specific behaviors:' . PHP_EOL;
// echo '   TrueStandard can only represent true' . PHP_EOL;
// echo '   FalseStandard can only represent false' . PHP_EOL;
// echo '   BoolStandard can represent both true and false' . PHP_EOL;
//
// echo PHP_EOL . '2. Stringable objects:' . PHP_EOL;
// $stringableTrue = new class implements Stringable {
//    public function __toString(): string
//    {
//        return 'true';
//    }
// };
// $stringableFalse = new class implements Stringable {
//    public function __toString(): string
//    {
//        return 'false';
//    }
// };
// echo '   tryFromMixed(Stringable "true"): ' . (BoolStandard::tryFromMixed($stringableTrue) instanceof BoolStandard ? 'BoolStandard' : 'Undefined') . PHP_EOL;
// echo '   tryFromMixed(Stringable "false"): ' . (BoolStandard::tryFromMixed($stringableFalse) instanceof BoolStandard ? 'BoolStandard' : 'Undefined') . PHP_EOL;
//
// echo PHP_EOL . '3. Existing instance handling:' . PHP_EOL;
// $existingBool = BoolStandard::fromBool(true);
// echo '   tryFromMixed(existing BoolStandard): ' . (BoolStandard::tryFromMixed($existingBool) instanceof BoolStandard ? 'BoolStandard' : 'Undefined') . PHP_EOL;
//
// echo PHP_EOL . '4. Undefined value() method usage (to satisfy Psalm):' . PHP_EOL;
// $undefinedInstance = UndefinedStandard::create();
// try {
//    $undefinedValue = $undefinedInstance->value();
// } catch (Exception) {
//    $undefinedValue = 'Undefined';
// }
// echo '   Undefined::value() returns: "' . $undefinedValue . '"' . PHP_EOL;
//
// // Additional usage of Undefined::value() to ensure it's not marked as unused
// try {
//    $undefinedValue2 = UndefinedStandard::create()->value();
//    $undefinedValue3 = Undefined::create()->value();
// } catch (Exception) {
//    $undefinedValue2 = 'Undefined';
//    $undefinedValue3 = 'Undefined';
// }
// // Store and use these values to avoid "unused variable" warnings
// $undefinedValues = [$undefinedValue, $undefinedValue2, $undefinedValue3];
// echo '   All undefined values collected: ' . count($undefinedValues) . PHP_EOL;
//
// echo PHP_EOL . '===== END BOOLEAN TYPES USAGE =====' . PHP_EOL;
//
// /**
// * Exercise BoolTypeInterface::value() for Psalm.
// */
// function testBool(BoolTypeInterface $b): bool
// {
//    return $b->value();
// }
//
// /**
// * Additional test functions to demonstrate interface method usage.
// */
// function testToBool(BoolTypeInterface $b): bool
// {
//    return $b->toBool();
// }
//
// function testToFloat(BoolTypeInterface $b): float
// {
//    return $b->toFloat();
// }
//
// function testToInt(BoolTypeInterface $b): int
// {
//    return $b->toInt();
// }
//
// // Call the test functions to ensure they're used
// $testBool = BoolStandard::fromBool(true);
// $toBoolResult = testToBool($testBool);
// $toFloatResult = testToFloat($testBool);
// $toIntResult = testToInt($testBool);
//
// // Also test with false value
// $testBoolFalse = BoolStandard::fromBool(false);
// $toBoolResultFalse = testToBool($testBoolFalse);
// $toFloatResultFalse = testToFloat($testBoolFalse);
// $toIntResultFalse = testToInt($testBoolFalse);
//
// // Test with TrueStandard and FalseStandard
// $trueStandardTest = TrueStandard::fromBool(true);
// $trueToBoolResult = testToBool($trueStandardTest);
// $trueToFloatResult = testToFloat($trueStandardTest);
// $trueToIntResult = testToInt($trueStandardTest);
//
// $falseStandardTest = FalseStandard::fromBool(false);
// $falseToBoolResult = testToBool($falseStandardTest);
// $falseToFloatResult = testToFloat($falseStandardTest);
// $falseToIntResult = testToInt($falseStandardTest);
//
// // Store and use all results to avoid "unused variable" warnings
// $allResults = [
//    'bool_true' => [$toBoolResult, $toFloatResult, $toIntResult],
//    'bool_false' => [$toBoolResultFalse, $toFloatResultFalse, $toIntResultFalse],
//    'true_standard' => [$trueToBoolResult, $trueToFloatResult, $trueToIntResult],
//    'false_standard' => [$falseToBoolResult, $falseToFloatResult, $falseToIntResult],
// ];
//
// echo PHP_EOL . 'All test results collected: ' . count($allResults) . ' test sets' . PHP_EOL;
