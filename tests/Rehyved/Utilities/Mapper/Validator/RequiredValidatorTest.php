<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class RequiredValidatorTest extends TestCase
{
    public function testGetReturnsCorrectAnnotationString(){
        $requiredValidator = new RequiredValidator();
        $this->assertEquals("required", $requiredValidator->getAnnotation());
    }

    public function testGetValidateShouldSucceedIfHasValue(){
        $requiredValidator = new RequiredValidator();
        $this->assertNull($requiredValidator->validate("", null));
        $this->assertNull($requiredValidator->validate(true, null));
        $this->assertNull($requiredValidator->validate(false, null));
        $this->assertNull($requiredValidator->validate(-1, null));
        $this->assertNull($requiredValidator->validate(0, null));
        $this->assertNull($requiredValidator->validate(1, null));
        $this->assertNull($requiredValidator->validate(array(), null));
        $this->assertNull($requiredValidator->validate(new \stdClass(), null));
    }

    public function testGetValidateShouldFailIfNull(){
    $requiredValidator = new RequiredValidator();
    $this->assertNotNull($requiredValidator->validate(null, null));
}
}