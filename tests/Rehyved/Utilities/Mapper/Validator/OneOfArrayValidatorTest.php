<?php

namespace Rehyved\Utilities\Mapper\Validator;


use PHPUnit\Framework\TestCase;

class OneOfArrayValidatorTest extends TestCase
{
    public function testReturnsCorrectAnnotationString()
    {
        $oneOfArrayValidator = new OneOfArrayValidator();
        $this->assertEquals("oneOf", $oneOfArrayValidator->getAnnotation());
    }

    public function testNullValidatesSuccess(){
        $oneOfArrayValidator = new OneOfArrayValidator();

        $this->assertNull($oneOfArrayValidator->validate(null, 1));
    }

    public function testValidateShouldSucceedIfValueInArray()
    {
        $oneOfArrayValidator = new OneOfArrayValidator();

        $this->assertNull($oneOfArrayValidator->validate(1, array(1)));
        $this->assertNull($oneOfArrayValidator->validate(1, array(2, 1)));

        $this->assertNull($oneOfArrayValidator->validate("a", array("a")));
        $this->assertNull($oneOfArrayValidator->validate("a", array("b", "a")));

        $this->assertNull($oneOfArrayValidator->validate(true, array(true)));
        $this->assertNull($oneOfArrayValidator->validate(true, array(false, true)));

        $this->assertNull($oneOfArrayValidator->validate(array(), array(array())));
        $this->assertNull($oneOfArrayValidator->validate(array(), array(array(1), array())));

        $this->assertNull($oneOfArrayValidator->validate("a", array("a", 2)));
    }

    public function testValidateShouldFailIfNotInArray()
    {
        $oneOfArrayValidator = new OneOfArrayValidator();

        $this->assertNotNull($oneOfArrayValidator->validate(1, array()));
        $this->assertNotNull($oneOfArrayValidator->validate(1, array(2, 3)));

        $this->assertNotNull($oneOfArrayValidator->validate("a", array()));
        $this->assertNotNull($oneOfArrayValidator->validate("a", array("b", "c")));

        $this->assertNotNull($oneOfArrayValidator->validate(array(), array(array(1))));

        $this->assertNotNull($oneOfArrayValidator->validate("1", array(1)));
    }
}