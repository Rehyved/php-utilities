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
     * Possible type coercion is taken into account if the $strict parameter is set to false (default) if not, possible
     * type coercion will be ignored and false will be returned if the type does not match exactly.
     *
     * @param $value mixed The value to check
     * @param string $type
     * @param bool $strict Indicates if the possibility of type coercion should be taken into account. If set to true
     * possible coercion be ignored
     * @return bool true if the type of the provided value is correct, false if the type of the value is not.
     */
    public static function isOfValidType($value, string $type, bool $strict = false)
    {
        if ($value === null) {
            return true;
        }

        if (self::isArrayType($type)) {
            return self::isValidArrayOfType($value, $type);
        }

        // The gettype function will return double instead of float, however the type from reflection might come back as
        // float.
        // See: http://php.net/manual/en/function.gettype.php
        $type = $type === "float" ? "double" : "" . $type;

        $valueType = gettype($value);
        $valueType = $valueType !== "object" ? $valueType : get_class($value);

        return $valueType === $type || (!$strict && self::isOfCoercibleType($value, $type));
    }

    private static function isValidArrayOfType($values, $type)
    {
        if (gettype($values) !== "array") {
            return false;
        }
        $valueType = self::getTypeOfArrayType($type);
        foreach ($values as $value) {
            if (!self::isOfValidType($value, $valueType)) {
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
        if ($type === "mixed") {
            return true;
        }
        $valueType = gettype($value);
        return $valueType === $type
            || ($valueType === "object" && get_class($value) === $type)
            || (self::isNumericType($type) && is_numeric($value))
            || ($type == "string" && is_numeric($value))
            || ($type === "bool" && self::isBooleanValue($value))
            || ($type == "string" && self::isBooleanValue($value));

    }

    private static function isBooleanStringValue($value): bool
    {
        return is_string($value) &&
            (StringHelper::equals($value, "true", true)
                || StringHelper::equals($value, "false", true));
    }

    private static function isBooleanNumericValue($value): bool
    {
        return is_numeric($value) && ($value == 1 || $value == 0);
    }

    /**
     * Checks if the provided value is a boolean value or a representation of a boolean value.
     *
     * @param $value mixed the value to check
     * @return bool true if the value is a boolean or a boolean representation, false if it is not
     */
    public static function isBooleanValue($value): bool
    {
        return is_bool($value) || self::isBooleanStringValue($value) || self::isBooleanNumericValue($value);
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
     * @throws TypeCoercionException
     */
    public static function coerceType($value, string $type)
    {
        if ($value === null) {
            return null;
        }

        if (self::isArrayType($type) && self::isValidArrayOfType($value, $type)) {
            $arrayType = self::getTypeOfArrayType($type);
            return array_map(function ($value) use ($arrayType) {
                return self::coerceType($value, $arrayType);
            }, $value);
        }

        switch ($type) {
            case "mixed":
                return $value;
            case "double":
            case "float":
                if (is_numeric($value)) {
                    return (float)$value;
                }
                break;
            case "int":
                if (is_numeric($value)) {
                    return (int)$value;
                }
                break;
            case "string":
                return is_bool($value) ? self::booleanStringValue($value) : (string)$value;
            case 'bool':
                return self::toBooleanValue($value);
            default:
                if (gettype($value) === $type || gettype($value) === "object" && get_class($value) === $type) {
                    return $value;
                }
                break;
        }
        throw new TypeCoercionException("Could not coerce to type '$type'.");
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
    public static function getTypeOfArrayType(string $type): string
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

    /**
     * Returns the type of the provided value
     * @param $value string the type of the value
     */
    public static function getType($value)
    {
        if ($value == null) {
            return null;
        }

        $type = gettype($value);
        if (is_array($value) && !empty($value)) {
            $arrayType = null;
            foreach ($value as $entry) {
                if ($arrayType == null) {
                    $entryType = gettype($entry);
                } else {
                    return "mixed[]";
                }
            }
            return $arrayType . "[]";
        }

        return $type;
    }


    public static function booleanStringValue(bool $value)
    {
        return $value ? "true" : "false";
    }

    /**
     * Converts the provided value to a bool. If the value is of the wrong type, has the wrong value or is null, null is returned.
     *
     * Accepted boolean values are:
     * - true
     * - false
     * - "true" (any casing accepted)
     * - "false" (any casing accepted)
     * - 0
     * - 1
     *
     * @param $value mixed the value to convert to a boolean
     * @return bool|null  bool value is returned if the value could be converted, null if $value was null
     * @throws TypeCoercionException when the value could not be coerced to a bool
     */
    public static function toBooleanValue($value)
    {
        if ($value === null || is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            if (StringHelper::equals($value, "true", true)) {
                return true;
            } elseif (StringHelper::equals($value, "false", true)) {
                return false;
            }
        } elseif (is_numeric($value)) {
            if ($value == 1) {
                return true;
            } elseif ($value == 0) {
                return false;
            }
        }
        throw new TypeCoercionException("Could not coerce to type 'bool'.");
    }
}