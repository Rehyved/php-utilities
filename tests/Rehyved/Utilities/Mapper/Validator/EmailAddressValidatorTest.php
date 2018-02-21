<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class EmailAddressValidatorTest extends TestCase
{
    const TEST_VALUE_NAME = "valueName";

    public function testReturnsCorrectAnnotationString(){
        $emailAddressValidator = new EmailAddressValidator();
        $this->assertEquals("email", $emailAddressValidator->getAnnotation());
    }

    public function testValidEmailAddressOrNullShouldValidate(){
        $validator = new EmailAddressValidator();
        $this->assertNull($validator->validate("test@example.org", null, self::TEST_VALUE_NAME));
        $this->expectException(\TypeError::class);
        $this->assertNull($validator->validate(null, null, null));
    }

    public function testInvalidEmailAddressShouldReturnValidationError(){
        $validator = new EmailAddressValidator();
        $this->assertNotNull(1, $validator->validate("@example.org", null, self::TEST_VALUE_NAME));
        $this->assertNotNull(1, $validator->validate("example.org", null, self::TEST_VALUE_NAME));
        $this->assertNotNull(1, $validator->validate("test@exampleorg", null, self::TEST_VALUE_NAME));
        $this->assertNotNull(1, $validator->validate("testexampleorg", null, self::TEST_VALUE_NAME));
        $this->assertNotNull(1, $validator->validate("", null, self::TEST_VALUE_NAME));
    }
}