<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class MinValidatorTest extends TestCase
{
    const TEST_VALUE_NAME = "valueName";

    public function testReturnsCorrectAnnotationString()
    {
        $minValidator = new MinValidator();
        $this->assertEquals("min", $minValidator->getAnnotation());
    }

    public function testNullValidatesSuccess(){
        $minValidator = new MinValidator();

        $this->assertNull($minValidator->validate(null, 1, self::TEST_VALUE_NAME));
    }

    public function testValidateShouldSucceedIfValueLargerOrEqual()
    {
        $minValidator = new MinValidator();

        $this->assertNull($minValidator->validate(1, 1, self::TEST_VALUE_NAME));
        $this->assertNull($minValidator->validate(2, 1, self::TEST_VALUE_NAME));

        $this->assertNull($minValidator->validate("a", 1, self::TEST_VALUE_NAME));
        $this->assertNull($minValidator->validate("ab", 1, self::TEST_VALUE_NAME));

        $this->assertNull($minValidator->validate(array("a"), 1, self::TEST_VALUE_NAME));
        $this->assertNull($minValidator->validate(array("a", "b"), 1, self::TEST_VALUE_NAME));

    }

    public function testValidateShouldFailIfLower()
    {
        $minValidator = new MinValidator();

        $this->assertNotNull($minValidator->validate(1, 2, self::TEST_VALUE_NAME));

        $this->assertNotNull($minValidator->validate("a", 2, self::TEST_VALUE_NAME));

        $this->assertNotNull($minValidator->validate(array("a"), 2, self::TEST_VALUE_NAME));
    }
}