<?php

namespace Rehyved\Utilities\Mapper\Validator;

use Rehyved\Utilities\Mapper\Validator\Error\MinValidationError;

class MinValidator implements IObjectMapperValidator
{
    public function getAnnotation(): string
    {
        return "min";
    }

    public function validate($value, $minValue, string $valueName)
    {
        if($value === null){
            return null;
        }

        if ((is_array($value) && count($value) < $minValue)
            || (is_string($value) && \mb_strlen($value) < $minValue)
            || (is_numeric($value) && $value < $minValue)
        ) {
            return new MinValidationError($valueName, $value, $minValue);
        }
        return null;
    }
}