<?php
namespace Rehyved\utilities\mapper;

interface IObjectMapperValidator
{
    public function getAnnotation() : string;
    public function validate($value, $annotationParameter);
}
