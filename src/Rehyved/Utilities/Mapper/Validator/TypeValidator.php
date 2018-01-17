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

class TypeValidator implements IObjectMapperValidator
{

    const ANNOTATION = "type";

    public function getAnnotation(): string
    {
        return self::ANNOTATION;
    }

    public function validate($value, $annotationParameter, $valueName = null)
    {
        return !self::isOfValidType($value, $annotationParameter) ? new TypeValidationError($valueName, $value) : null;
    }

    private static function isOfValidType($value, $type)
    {
        // The gettype function will return double instead of float, however the type from reflection might come back as float.
        // See: http://php.net/manual/en/function.gettype.php
        $type = $type === "float" ? "double" : "" . $type;

        $valueType = gettype($value);
        $valueType = $valueType !== "object" ? $valueType : get_class($value);

        return $valueType === $type || self::isOfCoercibleType($value, $type);
    }

    private static function isOfCoercibleType($value, $type): bool
    {
        return (self::isNumericType($type) && is_numeric($value)) || ($type === "bool" && self::isBooleanValue($value));
    }

    private static function isBooleanStringValue($value): bool
    {
        return is_string($value) && StringHelper::equals($value, "true", true) || StringHelper::equals($value, "false", true);
    }

    private static function isBooleanValue($value): bool
    {
        return is_bool($value) || self::isBooleanStringValue($value);
    }

    private static function isNumericType($type): bool
    {
        return $type === "int" || $type === "double" || $type === "float";
    }
}