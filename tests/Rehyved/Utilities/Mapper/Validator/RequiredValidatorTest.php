<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class RequiredValidatorTest extends TestCase
{
    const TEST_VALUE_NAME = "valueName";

    public function testReturnsCorrectAnnotationString(){
        $requiredValidator = new RequiredValidator();
        $this->assertEquals("required", $requiredValidator->getAnnotation());
    }

    public function testValidateShouldSucceedIfHasValue(){
        $requiredValidator = new RequiredValidator();
        $this->assertNull($requiredValidator->validate("", null, self::TEST_VALUE_NAME));
        $this->assertNull($requiredValidator->validate(true, null, self::TEST_VALUE_NAME));
        $this->assertNull($requiredValidator->validate(false, null, self::TEST_VALUE_NAME));
        $this->assertNull($requiredValidator->validate(-1, null, self::TEST_VALUE_NAME));
        $this->assertNull($requiredValidator->validate(0, null, self::TEST_VALUE_NAME));
        $this->assertNull($requiredValidator->validate(1, null, self::TEST_VALUE_NAME));
        $this->assertNull($requiredValidator->validate(array(), null, self::TEST_VALUE_NAME));
        $this->assertNull($requiredValidator->validate(new \stdClass(), null, self::TEST_VALUE_NAME));
    }

    public function testValidateShouldFailIfNull(){
    $requiredValidator = new RequiredValidator();
    $this->assertNotNull($requiredValidator->validate(null, null, self::TEST_VALUE_NAME));
}
}