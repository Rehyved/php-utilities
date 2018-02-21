<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class OneOfArrayValidatorTest extends TestCase
{
    const TEST_VALUE_NAME = "valueName";

    public function testReturnsCorrectAnnotationString()
    {
        $oneOfArrayValidator = new OneOfArrayValidator();
        $this->assertEquals("oneOf", $oneOfArrayValidator->getAnnotation());
    }

    public function testNullValidatesSuccess(){
        $oneOfArrayValidator = new OneOfArrayValidator();

        $this->assertNull($oneOfArrayValidator->validate(null, 1, self::TEST_VALUE_NAME));
    }

    public function testValidateShouldSucceedIfValueInArray()
    {
        $oneOfArrayValidator = new OneOfArrayValidator();

        $this->assertNull($oneOfArrayValidator->validate(1, array(1), self::TEST_VALUE_NAME));
        $this->assertNull($oneOfArrayValidator->validate(1, array(2, 1), self::TEST_VALUE_NAME));

        $this->assertNull($oneOfArrayValidator->validate("a", array("a"), self::TEST_VALUE_NAME));
        $this->assertNull($oneOfArrayValidator->validate("a", array("b", "a"), self::TEST_VALUE_NAME));

        $this->assertNull($oneOfArrayValidator->validate(true, array(true), self::TEST_VALUE_NAME));
        $this->assertNull($oneOfArrayValidator->validate(true, array(false, true), self::TEST_VALUE_NAME));

        $this->assertNull($oneOfArrayValidator->validate(array(), array(array()), self::TEST_VALUE_NAME));
        $this->assertNull($oneOfArrayValidator->validate(array(), array(array(1), array()), self::TEST_VALUE_NAME));

        $this->assertNull($oneOfArrayValidator->validate("a", array("a", 2), self::TEST_VALUE_NAME));
    }

    public function testValidateShouldFailIfNotInArray()
    {
        $oneOfArrayValidator = new OneOfArrayValidator();

        $this->assertNotNull($oneOfArrayValidator->validate(1, array(), self::TEST_VALUE_NAME));
        $this->assertNotNull($oneOfArrayValidator->validate(1, array(2, 3), self::TEST_VALUE_NAME));

        $this->assertNotNull($oneOfArrayValidator->validate("a", array(), self::TEST_VALUE_NAME));
        $this->assertNotNull($oneOfArrayValidator->validate("a", array("b", "c"), self::TEST_VALUE_NAME));

        $this->assertNotNull($oneOfArrayValidator->validate(array(), array(array(1)), self::TEST_VALUE_NAME));

        $this->assertNotNull($oneOfArrayValidator->validate("1", array(1), self::TEST_VALUE_NAME));
    }
}