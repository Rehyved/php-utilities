<?php
namespace Rehyved\Utilities\Mapper\Validator;

class MaxValidator implements IObjectMapperValidator
{
    public function getAnnotation() : string
    {
        return "max";
    }

    public function validate($value, $annotationParameter)
    {
        if (is_array($value) && count($value) > $annotationParameter) {
            throw new \Exception();
        } elseif (is_string($value) && \mb_strlen($value) > $annotationParameter) {
            throw new \Exception();
        } elseif (\is_numeric($value) && $value > $annotationParameter) {
            throw new \Exception();
        }
    }
}