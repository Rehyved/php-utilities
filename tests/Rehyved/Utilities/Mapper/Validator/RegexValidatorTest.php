<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class RegexValidatorTest extends TestCase
{
    const TEST_VALUE_NAME = "valueName";

    public function testReturnsCorrectAnnotationString()
    {
        $regexValidator = new RegexValidator();
        $this->assertEquals("matchesRegex", $regexValidator->getAnnotation());
    }

    public function testNullValidatesSuccess(){
        $regexValidator = new RegexValidator();

        $this->assertNull($regexValidator->validate(null, "/[0-9]/", self::TEST_VALUE_NAME));
    }

    public function testValidateShouldSucceedIfValueMatchesRegex()
    {
        $regexValidator = new RegexValidator();

        $this->assertNull($regexValidator->validate("1", "/[0-9]+$/", self::TEST_VALUE_NAME));
        $this->assertNull($regexValidator->validate("123456789", "/[0-9]+$/", self::TEST_VALUE_NAME));
    }

    public function testValidateShouldFailIfValueDoesNotMatchRegex()
    {
        $regexValidator = new RegexValidator();

        $this->assertNotNull($regexValidator->validate("1", "/[a-z,A-Z]+$/", self::TEST_VALUE_NAME));
        $this->assertNotNull($regexValidator->validate("abc1", "/[a-z,A-Z]+$/", self::TEST_VALUE_NAME));
    }
}