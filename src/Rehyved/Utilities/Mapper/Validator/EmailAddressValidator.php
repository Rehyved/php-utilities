<?php
namespace Rehyved\Utilities\Mapper\Validator;

class EmailAddressValidator extends RegexValidator
{
    public function getAnnotation() : string
    {
        return "email";
    }

    public function validate($value, $annotationParameter)
    {
        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
            throw new \Exception();
        }
    }
}