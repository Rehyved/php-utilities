<?php

namespace Rehyved\Utilities\Mapper\Validator;

interface IObjectMapperValidator
{
    public function getAnnotation(): string;

    public function validate($value, $annotationParameter, string $valueName);
}
