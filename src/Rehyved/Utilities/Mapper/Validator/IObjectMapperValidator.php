<?php
namespace Rehyved\Utilities\Mapper\Validator;

use Rehyved\Utilities\Mapper\Validator\Error\IValidationError;

interface IObjectMapperValidator
{
    public function getAnnotation() : string;
    public function validate($value, $annotationParameter, $valueName = null);
}
