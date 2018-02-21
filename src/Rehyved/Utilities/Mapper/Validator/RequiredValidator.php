<?php

namespace Rehyved\Utilities\Mapper\Validator;

use Rehyved\Utilities\Mapper\Validator\Error\RequiredValidationError;

class RequiredValidator implements IObjectMapperValidator
{
    public function getAnnotation(): string
    {
        return "required";
    }

    public function validate($value, $_, string $valueName)
    {
        if ($value === null) {
            return new RequiredValidationError($valueName, $value);
        }

        return null;
    }
}