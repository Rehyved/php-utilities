<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class MinValidatorTest extends TestCase
{
    public function testReturnsCorrectAnnotationString()
    {
        $minValidator = new MinValidator();
        $this->assertEquals("min", $minValidator->getAnnotation());
    }

    public function testNullValidatesSuccess(){
        $minValidator = new MinValidator();

        $this->assertNull($minValidator->validate(null, 1));
    }

    public function testValidateShouldSucceedIfValueLargerOrEqual()
    {
        $minValidator = new MinValidator();

        $this->assertNull($minValidator->validate(1, 1));
        $this->assertNull($minValidator->validate(2, 1));

        $this->assertNull($minValidator->validate("a", 1));
        $this->assertNull($minValidator->validate("ab", 1));

        $this->assertNull($minValidator->validate(array("a"), 1));
        $this->assertNull($minValidator->validate(array("a", "b"), 1));

    }

    public function testValidateShouldFailIfLower()
    {
        $minValidator = new MinValidator();

        $this->assertNotNull($minValidator->validate(1, 2));

        $this->assertNotNull($minValidator->validate("a", 2));

        $this->assertNotNull($minValidator->validate(array("a"), 2));
    }
}