<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class MaxValidatorTest extends TestCase
{
    public function testReturnsCorrectAnnotationString()
    {
        $maxValidator = new MaxValidator();
        $this->assertEquals("max", $maxValidator->getAnnotation());
    }

    public function testNullValidatesSuccess(){
        $maxValidator = new MaxValidator();

        $this->assertNull($maxValidator->validate(null, 1));
    }

    public function testValidateShouldSucceedIfValueLargerOrEqual()
    {
        $maxValidator = new MaxValidator();

        $this->assertNull($maxValidator->validate(1, 1));
        $this->assertNull($maxValidator->validate(0, 1));
        $this->assertNull($maxValidator->validate(-1, 1));

        $this->assertNull($maxValidator->validate("a", 1));
        $this->assertNull($maxValidator->validate("", 1));

        $this->assertNull($maxValidator->validate(array("a"), 1));
        $this->assertNull($maxValidator->validate(array(), 1));

    }

    public function testValidateShouldFailIfLower()
    {
        $maxValidator = new MaxValidator();

        $this->assertNotNull($maxValidator->validate(2, 1));

        $this->assertNotNull($maxValidator->validate("ab", 1));

        $this->assertNotNull($maxValidator->validate(array("a", "b"), 1));
    }
}