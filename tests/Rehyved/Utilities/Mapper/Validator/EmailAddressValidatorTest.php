<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class EmailAddressValidatorTest extends TestCase
{
    public function testGetReturnsCorrectAnnotationString(){
        $emailAddressValidator = new EmailAddressValidator();
        $this->assertEquals("email", $emailAddressValidator->getAnnotation());
    }

    public function testValidEmailAddressOrNullShouldValidate(){
        $validator = new EmailAddressValidator();
        $this->assertNull($validator->validate("test@example.org", null));
        $this->assertNull($validator->validate(null, null));
    }

    public function testInvalidEmailAddressShouldReturnValidationError(){
        $validator = new EmailAddressValidator();
        $this->assertNotNull(1, $validator->validate("@example.org", null));
        $this->assertNotNull(1, $validator->validate("example.org", null));
        $this->assertNotNull(1, $validator->validate("test@exampleorg", null));
        $this->assertNotNull(1, $validator->validate("testexampleorg", null));
        $this->assertNotNull(1, $validator->validate("", null));
    }
}