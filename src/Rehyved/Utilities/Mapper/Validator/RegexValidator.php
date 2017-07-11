<?php
namespace Rehyved\Utilities\Mapper\Validator;

class RegexValidator implements IObjectMapperValidator
{
    public function getAnnotation() : string
    {
        return "matchesRegex";
    }

    public function validate($value, $annotationParameter)
    {
        if(!is_string($value)){
            throw new \Exception();
        }

        if(!is_string($annotationParameter)){
            throw new \Exception();
        }

        if(preg_match($annotationParameter, $value) < 1){
            throw new \Exception();
        }

    }
}