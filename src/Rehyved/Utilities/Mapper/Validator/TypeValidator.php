<?php
/**
 * Created by Rehyved.
 * User: M.P. Waldhorst
 * Date: 1/3/2018
 * Time: 11:49 PM
 */

namespace Rehyved\Utilities\Mapper\Validator;


class TypeValidator implements IObjectMapperValidator
{

    public function getAnnotation(): string
    {
        return "type";
    }

    public function validate($value, $annotationParameter, $valueName = null)
    {
        return self::isOfValidType($value, $annotationParameter);
    }

    private static function isOfValidType($value, $type)
    {
        // The gettype function will return double instead of float, however the type from reflection might come back as float.
        // See: http://php.net/manual/en/function.gettype.php
        $type = $type === "float" ? "double" : "" . $type;

        return gettype($value) === $type || self::isOfCoercibleType($value, $type);
    }
}