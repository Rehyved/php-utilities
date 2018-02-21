<?php
namespace Rehyved\Utilities\Mapper\Validator;

use Rehyved\Utilities\Mapper\Validator\Error\RegexValidationError;

class RegexValidator implements IObjectMapperValidator
{
    public function getAnnotation() : string
    {
        return "matchesRegex";
    }

    public function validate($value, $regex, string $valueName)
    {
        if($value === null){
            return null;
        }

        if(!is_string($value)){
            throw new \InvalidArgumentException("The value checked on the regex annotation for regex '$regex' is not a valid string. (name of value '$valueName')");
        }

        if(!is_string($regex)){
            throw new \InvalidArgumentException("The regex on the regex annotation is not a string and thus no regex. (name of value '$valueName')");
        }

        if(preg_match($regex, $value) < 1){
            return new RegexValidationError($valueName, $value, $regex);
        }

        return null;
    }
}