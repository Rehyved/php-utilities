<?php
namespace Rehyved\utilities\mapper\validator;

interface IObjectMapperValidator
{
    public function getAnnotation() : string;
    public function validate($value, $annotationParameter);
}
