<?php
namespace Rehyved\Utilities\Mapper\Validator;

class OneOfArrayValidator implements IObjectMapperValidator
{
    public function getAnnotation() : string
    {
        return "oneOf";
    }

    public function validate($value, $annotationParameter)
    {
        if(!is_array($annotationParameter)){
            throw new \Exception();
        }

        if(!in_array($value, $annotationParameter)){
            throw new \Exception();
        }

    }
}