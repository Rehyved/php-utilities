<?php

namespace Rehyved\Utilities\Mapper\Validator;

use Rehyved\Utilities\Mapper\Validator\Error\MaxLengthValidationError;
use Rehyved\Utilities\Mapper\Validator\Error\MaxValidationError;

class MaxValidator implements IObjectMapperValidator
{
    public function getAnnotation(): string
    {
        return "max";
    }

    public function validate($value, $maxValue, string $valueName)
    {
        if($value === null){
            return null;
        }

        if ((is_array($value) && count($value) > $maxValue)
            || (is_string($value) && \mb_strlen($value) > $maxValue)
        ) {
            return new MaxLengthValidationError($valueName, $value, $maxValue);
        }elseif(is_numeric($value) && $value > $maxValue){
            return new MaxValidationError($valueName, $value, $maxValue);
        }
        return null;
    }
}