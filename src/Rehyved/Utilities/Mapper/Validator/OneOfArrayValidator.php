<?php

namespace Rehyved\Utilities\Mapper\Validator;

use Rehyved\Utilities\Mapper\Validator\Error\OneOfArrayValidationError;

class OneOfArrayValidator implements IObjectMapperValidator
{
    public function getAnnotation(): string
    {
        return "oneOf";
    }

    public function validate($value, $array, $valueName = null)
    {
        if($value === null){
            return null;
        }
        if (!is_array($array)) {
            throw new \InvalidArgumentException("The value used in the oneOf annotation is not an array. (name of value: '$valueName')");
        }

        if (!in_array($value, $array, true)) {
            return new OneOfArrayValidationError($value, $value, $array);
        }

        return null;
    }
}