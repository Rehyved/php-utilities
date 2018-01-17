<?php
/**
 * Created by Rehyved.
 * User: M.P. Waldhorst
 * Date: 1/17/2018
 * Time: 2:27 PM
 */

namespace Rehyved\Utilities;


class TypeHelper
{
    /**
     * Validates if the given value is of the provided type.
     * This also works for types like ClassName[] in which case the value is checked to be of type array and each value
     * in the array will be checked against the type ClassName.
     *
     * @param $value mixed The value to check
     * @param string $type
     * @return bool true if the type of the provided value is correct, false if the type of the value is not.
     */
    public static function isOfValidType($value, string $type)
    {
        if($value === null){
            return true;
        }

        if (self::isArrayType($type)) {
            return self::isValidArrayOfType($value, $type);
        }

        // The gettype function will return double instead of float, however the type from reflection might come back as float.
        // See: http://php.net/manual/en/function.gettype.php
        $type = $type === "float" ? "double" : "" . $type;

        $valueType = gettype($value);
        $valueType = $valueType !== "object" ? $valueType : get_class($value);

        return $valueType === $type || self::isOfCoercibleType($value, $type);
    }

    private static function isValidArrayOfType($values, $type)
    {
        if (gettype($values) !== "array") {
            return false;
        }
        $valueType = self::getTypeOfArrayType($type);
        foreach ($values as $value) {
            if (!self::isOfValidType($value, $valueType)) {
                var_dump($value, $valueType);
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the provided value can be coerced to the provided type.
     * This method is intended for use with primitive values.
     *
     * @param $value mixed The value to check
     * @param $type string The type to check against
     * @return bool true if the provided value can be coerced to the provided type, false if not
     */
    public static function isOfCoercibleType($value, string $type): bool
    {
        return $type === "mixed"
            || gettype($value) === $type
            || (self::isNumericType($type) && is_numeric($value))
            || ($type === "bool" && self::isBooleanValue($value));
    }

    private static function isBooleanStringValue($value): bool
    {
        return is_string($value) && StringHelper::equals($value, "true", true) || StringHelper::equals($value, "false", true);
    }

    /**
     * Checks if the provided value is a boolean value or a representation of a boolean value.
     *
     * @param $value mixed the value to check
     * @return bool true if the value is a boolean or a boolean representation, false if it is not
     */
    public static function isBooleanValue($value): bool
    {
        return is_bool($value) || self::isBooleanStringValue($value);
    }

    /**
     * Checks if the provided value is a numeric value or a representation of a numeric value.
     *
     * @param $type mixed the value to check
     * @return bool true if the value is a numeric value or a representation of a numeric value, false if not
     */
    public static function isNumericType($type): bool
    {
        return $type === "int" || $type === "double" || $type === "float";
    }

    /**
     * Coerces the provided value to the provided type.
     * If the type does not match either bool, float, double, int or string the same value is returned.
     *
     * @param mixed $value the value to coerce
     * @param string $type the type to coerce to if possible
     * @return bool|float|int|string|mixed  the coerced value or the provided value if the type was not bool, float,
     *                                      double, int or string
     */
    public static function coerceType($value, string $type)
    {
        switch ($type) {
            case "double":
            case "float":
                return (float)$value;
            case "int":
                return (int)$value;
            case "string":
                return (string)$value;
            case 'bool':
                return is_bool($value) ? $value : StringHelper::equals($value, "true", true);
            default:
                return $value;

        }
    }

    /**
     * Checks if the provided type is an array type.
     *
     * @param $type string the type to check
     * @return bool true if the type contains [] to indicate it is an array type, false if it does not
     */
    public static function isArrayType(string $type): bool
    {
        return StringHelper::endsWith($type, "[]");
    }

    /**
     * Returns the type of the provided array type.
     * If it is not an array type the same value is returned.
     *
     * @param string $type the array type to get the type from.
     * @return string The type of the array type or the provided type if it was not an array type.
     */
    public static function getTypeOfArrayType(string $type) : string
    {
        return str_replace("[]", "", $type);
    }


    /**
     * Checks if the provided type is a built-in primitive type or mixed.
     * @param $type string the type to check
     * @return bool true if the type is a primitive built-in type or mixed.
     */
    public static function isBuiltInType($type)
    {
        switch ($type) {
            case "mixed":
            case "double":
            case "float":
            case "int":
            case "string":
            case "array":
            case 'bool':
                return true;
            default:
                return false;
        }
    }
}