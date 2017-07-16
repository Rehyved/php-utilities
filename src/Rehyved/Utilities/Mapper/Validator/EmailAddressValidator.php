<?php
namespace Rehyved\Utilities\Mapper\Validator;

use Rehyved\Utilities\Mapper\Validator\Error\EmailAddressValidationError;

class EmailAddressValidator implements IObjectMapperValidator
{
    public function getAnnotation() : string
    {
        return "email";
    }

    public function validate($value, $_, $valueName = null)
    {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
            return new EmailAddressValidationError($valueName, $value);
        }
        return null;
    }
}