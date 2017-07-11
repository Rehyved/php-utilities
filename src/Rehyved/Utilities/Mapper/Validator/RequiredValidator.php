<?php
namespace Rehyved\Utilities\Mapper\Validator;

class RequiredValidator implements IObjectMapperValidator
{
    public function getAnnotation() : string
    {
        return "required";
    }

    public function validate($value, $annotationParameter)
    {
        if(empty($value) || $value === false){
            throw new \Exception();
        }
    }
}