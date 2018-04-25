<?php
/**
 * Created by Rehyved.
 * User: M.P. Waldhorst
 * Date: 4/24/2018
 * Time: 9:36 PM
 */

namespace Rehyved\Utilities;


use PHPUnit\Framework\TestCase;

class TestClass
{

}

class TestClass2
{

}

class TypeHelperTest extends TestCase
{
    public function isOfValidTypeProvider()
    {
        return [
            [null, "int", false, true],
            [1, "int", false, true],
            ["1", "int", false, true],
            ["1", "int", true, false],
            [true, "int", false, false],
            [new TestClass(), "int", false, false],
            ["", "int", false, false],
            ["a", "int", false, false],
            [array(0), "int", false, false],

            [null, "float", false, true],
            [0.5, "float", false, true],
            ["0.5", "float", false, true],
            ["0.5", "float", true, false],
            [1, "float", false, true],
            ["1", "float", false, true],
            ["1", "float", true, false],
            [true, "float", false, false],
            [new TestClass(), "float", false, false],
            ["", "float", false, false],
            ["a", "float", false, false],
            [array(0), "float", false, false],

            [null, "double", false, true],
            [0.5, "double", false, true],
            ["0.5", "double", false, true],
            ["0.5", "double", true, false],
            [1, "double", false, true],
            ["1", "double", false, true],
            ["1", "double", true, false],
            [true, "double", false, false],
            [new TestClass(), "double", false, false],
            ["", "double", false, false],
            ["a", "double", false, false],
            [array(0), "double", false, false],

            [null, "string", false, true],
            ["", "string", false, true],
            ["test", "string", false, true],
            [1, "string", false, true],
            [1, "string", true, false],
            [0.5, "string", false, true],
            [0.5, "string", true, false],
            [true, "string", false, true],
            [true, "string", true, false],
            [new TestClass(), "string", false, false],
            [array("test"), "string", false, false],
            [array(0), "string", false, false],
            [array(0.5), "string", false, false],
            [array(new TestClass()), "string", false, false],

            [null, TestClass::class, false, true],
            [new TestClass(), TestClass::class, false, true],
            [1, TestClass::class, false, false],
            [0.5, TestClass::class, false, false],
            ["", TestClass::class, false, false],
            ["a", TestClass::class, false, false],
            [new TestClass2(), TestClass::class, false, false],
            [array(new TestClass()), TestClass::class, false, false],


            [array(), "array", false, true],
            [array(1, 2, 3), "array", false, true],
            [array(1, 2, 3), "int[]", false, true],
            [array(1, "", true, new TestClass()), "array", false, true],
            [array(new TestClass()), "array", false, true],
            [array(new TestClass()), TestClass::class . "[]", false, true],
            [array(1, "", true, new TestClass()), "int[]", false, false],
            [array(1, "", true, new TestClass()), TestClass::class . "[]", false, false],
            [array(new TestClass2()), TestClass::class . "[]", false, false],

        ];
    }

    /**
     * @dataProvider isOfValidTypeProvider
     * @param $value
     * @param $expectedType
     * @param $expectedResult
     */
    public function testIsOfValidType($value, $expectedType, $strict, $expectedResult)
    {
        $this->assertEquals($expectedResult, TypeHelper::isOfValidType($value, $expectedType, $strict));
    }

    public function isOfCoercibleTypeProvider()
    {
        return [
            [1, "int", true],
            ["1", "int", true],
            ["", "int", false],
            [1.5, "int", true], // Casting is possible, but loss of precision
            ["1.5", "int", true], // Casting is possible, but loss of precision
            [array(), "int", false],
            [array(1), "int", false],
            [new TestClass(), "int", false],
            [true, "int", false],

            [1, "float", true],
            ["1", "float", true],
            ["", "float", false],
            [1.5, "float", true],
            ["1.5", "float", true],
            [array(), "float", false],
            [array(0.5), "float", false],
            [new TestClass(), "float", false],
            [true, "float", false],

            [1, "double", true],
            ["1", "double", true],
            ["", "double", false],
            [1.5, "double", true],
            ["1.5", "double", true],
            [array(), "double", false],
            [array(0.5), "double", false],
            [new TestClass(), "double", false],
            [true, "double", false],

            ["", "string", true],
            ["a", "string", true],
            [1, "string", true],
            [0.5, "string", true],
            [true, "string", true],
            [true, "string", true],
            [array(), "string", false],
            [array(""), "string", false],
            [new TestClass(), "string", false],

            [true, 'bool', true],
            [false, 'bool', true],
            ["true", 'bool', true],
            ["false", 'bool', true],
            ["True", 'bool', true],
            ["False", 'bool', true],
            ["TRUE", 'bool', true],
            ["FALSE", 'bool', true],
            [0, 'bool', true],
            [1, 'bool', true],
            ["", 'bool', false],
            ["a", 'bool', false],
            [array(), 'bool', false],
            [array(true), 'bool', false],
            [array(false), 'bool', false],
            [new TestClass(), 'bool', false],

            [new TestClass(), TestClass::class, true],
            [TestClass::class, TestClass::class, false],
            ["", TestClass::class, false],
            ["a", TestClass::class, false],
            [1, TestClass::class, false],
            [0.5, TestClass::class, false],
            [true, TestClass::class, false],
            [false, TestClass::class, false]
        ];
    }

    /**
     * @dataProvider isOfCoercibleTypeProvider
     * @param $value
     * @param $expectedType
     * @param $expectedResult
     */
    public function testIsOfCoercibleType($value, $expectedType, $expectedResult)
    {
        $this->assertEquals($expectedResult, TypeHelper::isOfCoercibleType($value, $expectedType));
    }

    public function isBooleanValueProvider()
    {
        return [
            [true, true],
            [false, true],
            ["true", true],
            ["false", true],
            ["True", true],
            ["False", true],
            ["TRUE", true],
            ["FALSE", true],
            [0, true],
            [1, true],
            ["", false],
            ["a", false],
            [array(), false],
            [array(true), false],
            [array(false), false],
            [new TestClass(), false],
        ];
    }

    /**
     * @dataProvider isBooleanValueProvider
     * @param $value
     * @param $expectedResult
     */
    public function testIsBooleanValue($value, $expectedResult)
    {
        $this->assertEquals($expectedResult, TypeHelper::isBooleanValue($value));
    }


    public function isNumericTypeProvider()
    {
        return [
            ['int', true],
            ['float', true],
            ['double', true],

            ['string', false],
            ['bool', false],
            ['mixed', false],
            ['array', false],
            [TestClass::class, false],
        ];
    }

    /**
     * @dataProvider isNumericTypeProvider
     * @param $type
     * @param $expectedResult
     */
    public function testIsNumericType($type, $expectedResult)
    {
        $this->assertEquals($expectedResult, TypeHelper::isNumericType($type));
    }

    public function coerceTypeProvider()
    {
        $testClassInstance = new TestClass();
        $arrayInstance = array(1, 2, 3);
        $arrayOfClassInstance = array(new TestClass(), new TestClass());
        return [
            ["a", "mixed", "a"],

            [null, 'float', null],
            [1, 'float', 1.0],
            ["1", 'float', 1.0],
            [0.5, 'float', 0.5],
            ["0.5", 'float', 0.5],
            ["", 'float', null, TypeCoercionException::class],
            ["a", 'float', null, TypeCoercionException::class],
            [true, "float", null, TypeCoercionException::class],
            [false, "float", null, TypeCoercionException::class],
            [array(), "float", null, TypeCoercionException::class],
            [new TestClass(), "float", null, TypeCoercionException::class],

            [null, 'double', null],
            [1, 'double', 1.0],
            ["1", 'double', 1.0],
            [0.5, 'double', 0.5],
            ["0.5", 'double', 0.5],
            ["", 'double', null, TypeCoercionException::class],
            ["a", 'double', null, TypeCoercionException::class],
            [true, "double", null, TypeCoercionException::class],
            [false, "double", null, TypeCoercionException::class],
            [array(), "double", null, TypeCoercionException::class],
            [new TestClass(), "double", null, TypeCoercionException::class],

            [null, 'int', null],
            [1, 'int', 1],
            ["1", 'int', 1],
            ["1.5", 'int', 1],
            ["", 'int', null, TypeCoercionException::class],
            ["a", 'int', null, TypeCoercionException::class],
            [true, "int", null, TypeCoercionException::class],
            [false, "int", null, TypeCoercionException::class],
            [array(), "int", null, TypeCoercionException::class],
            [new TestClass(), "int", null, TypeCoercionException::class],

            [null, 'string', null],
            ["", "string", ""],
            ["a", "string", "a"],
            [1, "string", "1"],
            [0.5, "string", "0.5"],
            [true, "string", "true"],
            [false, "string", "false"],

            [null, 'bool', null],
            [true, "bool", true],
            [false, "bool", false],
            ["true", "bool", true],
            ["false", "bool", false],
            ["True", "bool", true],
            ["False", "bool", false],
            ["TRUE", "bool", true],
            ["FALSE", "bool", false],
            [1, "bool", true],
            [0, "bool", false],
            ["", "bool", null, TypeCoercionException::class],
            ["a", "bool", null, TypeCoercionException::class],

            [$arrayInstance, "array", $arrayInstance],
            [$arrayInstance, "int[]", $arrayInstance],
            [$arrayOfClassInstance, TestClass::class . "[]", $arrayOfClassInstance],
            [array(new TestClass(), 1), TestClass::class . "[]", null, TypeCoercionException::class],

            [$testClassInstance, TestClass::class, $testClassInstance],
            [1, TestClass::class, null, TypeCoercionException::class],
            [0.5, TestClass::class, null, TypeCoercionException::class],
            ["", TestClass::class, null, TypeCoercionException::class],
            ["a", TestClass::class, null, TypeCoercionException::class],
            [true, TestClass::class, null, TypeCoercionException::class],
            [array(), TestClass::class, null, TypeCoercionException::class],
            [array($testClassInstance), TestClass::class, null, TypeCoercionException::class]
        ];

    }

    /**
     * @dataProvider coerceTypeProvider
     * @param $value
     * @param $coercionType
     * @param $expectedValue
     */
    public function testCoerceType($value, $coercionType, $expectedValue, $expectedExceptionType = null)
    {
        if ($expectedExceptionType === null) {
            $this->assertEquals($expectedValue, TypeHelper::coerceType($value, $coercionType));
        } else {
            $this->expectException($expectedExceptionType);
            TypeHelper::coerceType($value, $coercionType);
        }
    }

    public function isTypedArrayTypeProvider()
    {
        return [
            ["int[]", true],
            [TestClass::class . "[]", true],

            ["array", false],
            ["int", false],
            [TestClass::class, false],
        ];
    }

    /**
     * @dataProvider isTypedArrayTypeProvider
     * @param string $type
     * @param bool $expectedResult
     */
    public function testIsTypedArrayType(string $type, bool $expectedResult)
    {
        $this->assertEquals($expectedResult, TypeHelper::isTypedArrayType($type));
    }

    public function isBuiltInTypeProvider()
    {
        return [
            ['mixed', true],
            ['float', true],
            ['int', true],
            ['string', true],
            ['array', true],
            ['bool', true],

            [TestClass::class, false],
            ["", false],
            ["a", false],
            [true, false],
        ];
    }

    /**
     * @dataProvider isBuiltInTypeProvider
     * @param $type
     * @param $expectedResult
     */
    public function testIsBuiltInType($type, $expectedResult)
    {
        $this->assertEquals($expectedResult, TypeHelper::isBuiltInType($type));
    }

    public function getTypeProvider()
    {
        return [
            ["", 'string'],
            ["a", 'string'],
            [1, 'int'],
            [0.5, 'float'],
            [true, 'bool'],
            [false, 'bool'],
            [new TestClass(), TestClass::class],
            [array(), 'array'],
            [array(1), 'int[]'],
            [array(1, ""), 'mixed[]'],
            [array(new TestClass()), TestClass::class . '[]'],
            [array(array(1)), 'int[][]'],
            [array(1, array(1)), 'mixed[]']
        ];
    }

    /**
     * @dataProvider getTypeProvider
     * @param $value
     * @param $expectedType
     */
    public function testGetType($value, $expectedType)
    {
        $this->assertEquals($expectedType, TypeHelper::getType($value));
    }

    public function testBooleanStringValue()
    {
        $this->assertEquals("true", TypeHelper::booleanStringValue(true));
        $this->assertEquals("false", TypeHelper::booleanStringValue(false));
    }

    public function toBooleanValueProvider()
    {
        return [
            // Passthrough:
            [null, null],
            [true, true],
            [false, false],

            // Coercion positive:
            [1, true],
            [0, false],
            ["true", true],
            ["TRUE", true],
            ["True", true],
            ["false", false],
            ["FALSE", false],
            ["False", false],

            // Coercion negative:
            ["", false, TypeCoercionException::class],
            ["a", false, TypeCoercionException::class],
            [2, null, TypeCoercionException::class],
            [-1, null, TypeCoercionException::class],
        ];
    }

    /**
     * @dataProvider toBooleanValueProvider
     * @param $value
     * @param $expectedResult
     */
    public function testToBooleanValue($value, $expectedResult, $expectedExceptionType = null)
    {
        if ($expectedExceptionType === null) {
            $this->assertEquals($expectedResult, TypeHelper::toBooleanValue($value));
        } else {
            $this->expectException($expectedExceptionType);
            TypeHelper::toBooleanValue($value);
        }
    }

    public function mapToBuiltInTypeProvider()
    {
        return [
            ['double', 'float'],
            ['integer', 'int'],
            ['boolean', 'bool'],
            ['string', 'string'],
            ['array', 'array'],
            ['mixed', 'mixed'],
            [TestClass::class, TestClass::class],
            ['int[]', 'int[]'],
        ];
    }

    /**
     * @dataProvider mapToBuiltInTypeProvider
     * @param $type
     * @param $expectedBuiltInType
     */
    public function testMapToBuiltInType($type, $expectedBuiltInType)
    {
        $this->assertEquals($expectedBuiltInType, TypeHelper::mapToBuiltInType($type));
    }

}