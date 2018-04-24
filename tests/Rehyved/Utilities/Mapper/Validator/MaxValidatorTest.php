<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;
use Rehyved\Utilities\Mapper\Validator\Error\MaxLengthValidationError;
use Rehyved\Utilities\Mapper\Validator\Error\MaxValidationError;

class MaxValidatorTest extends TestCase
{
    const TEST_VALUE_NAME = "valueName";

    public function testReturnsCorrectAnnotationString()
    {
        $maxValidator = new MaxValidator();
        $this->assertEquals("max", $maxValidator->getAnnotation());
    }

    public function testNullValidatesSuccess(){
        $maxValidator = new MaxValidator();

        $this->assertNull($maxValidator->validate(null, 1, self::TEST_VALUE_NAME));
    }

    public function testValidateShouldSucceedIfValueLargerOrEqual()
    {
        $maxValidator = new MaxValidator();

        $this->assertNull($maxValidator->validate(1, 1, self::TEST_VALUE_NAME));
        $this->assertNull($maxValidator->validate(0, 1, self::TEST_VALUE_NAME));
        $this->assertNull($maxValidator->validate(-1, 1, self::TEST_VALUE_NAME));

        $this->assertNull($maxValidator->validate("a", 1, self::TEST_VALUE_NAME));
        $this->assertNull($maxValidator->validate("", 1, self::TEST_VALUE_NAME));

        $this->assertNull($maxValidator->validate(array("a"), 1, self::TEST_VALUE_NAME));
        $this->assertNull($maxValidator->validate(array(), 1, self::TEST_VALUE_NAME));

    }

    public function testValidateShouldFailIfLower()
    {
        $maxValidator = new MaxValidator();

        $numericMaxValidationError = $maxValidator->validate(2, 1, self::TEST_VALUE_NAME);
        $this->assertNotNull($numericMaxValidationError);
        $this->assertInstanceOf(MaxValidationError::class, $numericMaxValidationError);

        $stringMaxValidationError = $maxValidator->validate("ab", 1, self::TEST_VALUE_NAME);
        $this->assertNotNull($stringMaxValidationError);
        $this->assertInstanceOf(MaxLengthValidationError::class, $stringMaxValidationError);

        $arrayMaxValidationError = $maxValidator->validate(array("a", "b"), 1, self::TEST_VALUE_NAME);
        $this->assertNotNull($arrayMaxValidationError);
        $this->assertInstanceOf(MaxLengthValidationError::class, $arrayMaxValidationError);
    }
}