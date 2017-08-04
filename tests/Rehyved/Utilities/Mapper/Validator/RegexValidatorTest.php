<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class RegexValidatorTest extends TestCase
{
    public function testReturnsCorrectAnnotationString()
    {
        $regexValidator = new RegexValidator();
        $this->assertEquals("matchesRegex", $regexValidator->getAnnotation());
    }

    public function testNullValidatesSuccess(){
        $regexValidator = new RegexValidator();

        $this->assertNull($regexValidator->validate(null, "/[0-9]/"));
    }

    public function testValidateShouldSucceedIfValueMatchesRegex()
    {
        $regexValidator = new RegexValidator();

        $this->assertNull($regexValidator->validate("1", "/[0-9]+$/"));
        $this->assertNull($regexValidator->validate("123456789", "/[0-9]+$/"));
    }

    public function testValidateShouldFailIfValueDoesNotMatchRegex()
    {
        $regexValidator = new RegexValidator();

        $this->assertNotNull($regexValidator->validate("1", "/[a-z,A-Z]+$/"));
        $this->assertNotNull($regexValidator->validate("abc1", "/[a-z,A-Z]+$/"));
    }
}