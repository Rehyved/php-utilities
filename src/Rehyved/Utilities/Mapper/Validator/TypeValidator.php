<?php
/**
 * Created by Rehyved.
 * User: M.P. Waldhorst
 * Date: 1/3/2018
 * Time: 11:49 PM
 */

namespace Rehyved\Utilities\Mapper\Validator;


use Rehyved\Utilities\Mapper\Validator\Error\TypeValidationError;
use Rehyved\Utilities\StringHelper;
use Rehyved\Utilities\TypeHelper;

class TypeValidator implements IObjectMapperValidator
{

    const ANNOTATION = "var";

    public function getAnnotation(): string
    {
        return self::ANNOTATION;
    }

    public function validate($value, $annotationParameter, $valueName = null)
    {
        return !TypeHelper::isOfValidType($value, $annotationParameter) ? new TypeValidationError($valueName, $value) : null;
    }
}