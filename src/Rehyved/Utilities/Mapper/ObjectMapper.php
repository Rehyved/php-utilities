<?php

namespace Rehyved\Utilities\Mapper;

use DocBlockReader\Reader;
use Rehyved\Utilities\Mapper\Validator\EmailAddressValidator;
use Rehyved\Utilities\Mapper\validator\IObjectMapperValidator;
use Rehyved\Utilities\Mapper\Validator\MaxValidator;
use Rehyved\Utilities\Mapper\Validator\MinValidator;
use Rehyved\Utilities\Mapper\Validator\OneOfArrayValidator;
use Rehyved\Utilities\Mapper\Validator\RegexValidator;
use Rehyved\Utilities\Mapper\Validator\RequiredValidator;
use Rehyved\Utilities\StringHelper;

class ObjectMapper implements IObjectMapper
{
    const ARRAY_OF_TYPE_ANNOTATION = "arrayOf";
    const PATH_DELIMITER = "_";

    private static $PHP_DOC_ANNOTATIONS = array(
        "api",
        "author",
        "category",
        "copyright",
        "deprecated",
        "example",
        "filesource",
        "global",
        "ignore",
        "internal",
        "license",
        "link",
        "method",
        "package",
        "param",
        "property",
        "property-read",
        "property-write",
        "return",
        "see",
        "since",
        "source",
        "subpackage",
        "throws",
        "todo",
        "uses",
        "var",
        "version"
    );

    private $validators = array();

    private $failFastValidation;
    private $lenientTypeCheck;

    public function __construct()
    {
        $this->failFastValidation = false;
        $this->lenientTypeCheck = true;

        // Add default set of validators
        $this->addValidator(new MinValidator());
        $this->addValidator(new MaxValidator());
        $this->addValidator(new RequiredValidator());
        $this->addValidator(new RegexValidator());
        $this->addValidator(new OneOfArrayValidator());
        $this->addValidator(new EmailAddressValidator());
    }

    /**
     * @param mixed $failFastValidation
     */
    public function setFailFastValidation($failFastValidation)
    {
        $this->failFastValidation = $failFastValidation;
    }

    /**
     * @param mixed $lenientTypeCheck
     */
    public function setLenientTypeCheck($lenientTypeCheck)
    {
        $this->lenientTypeCheck = $lenientTypeCheck;
    }

    public function addValidator(IObjectMapperValidator $validator)
    {
        if (self::isExcludedAnnotation($validator->getAnnotation())) {
            throw new \InvalidArgumentException(
                "Cannot add a validator with the name '"
                . $validator->getAnnotation()
                . "' this annotation name is used for indicating array object types or part of general phpDoc specification. Choose a different "
                . "annotation name for this validator."
            );
        }
        $this->validators[$validator->getAnnotation()] = $validator;
    }

    /**
     * Maps the provided array to an instance of the provided type.
     * During mapping the validators are used to validate the parameters and values of the resulting object.
     * Based on the fail fast validation setting this function will fail immediately or at the end with the mapping run
     * with a list of validation errors.
     *
     * @param array $array The array containing the data to map to the object.
     * @param string $type The type of the object of which an instance should be created and mapped to.
     * @param string $prefix The prefix used in the array for the keys of the object.
     * @return mixed An instance of the provided type.
     */
    public function mapArrayToObject(array $array, string $type, string $prefix = "")
    {
        return $this->doMapArrayToType($array, $type, $prefix, "");
    }

    private function doMapArrayToType(array $array, string $type, string $prefix, string $parentKey)
    {
        $objectToFill = new $type();
        $reflectionClass = new \ReflectionClass($objectToFill);

        $setters = array_filter($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC), "self::isSetter");

        if (empty($setters)) {
            throw new ObjectMappingException("The provided class does not contain any setters.");
        }

        $validationErrors = array();
        foreach ($setters as $setter) {

            $propertyName = self::getPropertyName($setter);

            $propertyType = self::getPropertyType($setter);
            $propertyKey = empty($prefix) ? $propertyName : $prefix . self::PATH_DELIMITER . $propertyName;

            $annotationReader = new Reader($reflectionClass->getName(), $setter->getName());
            $annotations = $annotationReader->getParameters();


            if (!array_key_exists($propertyKey, $array)) {
                // TODO: call check annotations for getter to validate requirements
                continue;
            }

            if ($propertyType !== null && self::isCustomType($propertyType)) {
                try {
                    $customType = "" . $propertyType;
                    $propertyValue = $this->doMapArrayToType($array[$propertyKey], $customType, "", "");

                    $this->checkAnnotations($propertyValue, $annotations, $propertyKey, $parentKey);

                    $setter->invoke($objectToFill, $propertyValue);
                } catch (ObjectMappingException $e) {
                    if (!$this->failFastValidation && !empty($e->getValidationErrors())) {
                        $validationErrors = array_merge($validationErrors, $e->getValidationErrors());
                    } else {
                        throw $e;
                    }
                }
            } else if (array_key_exists(self::ARRAY_OF_TYPE_ANNOTATION, $annotations)) {
                // TODO: support simple types
                try {
                    $checkedArray = null;

                    $valueType = $annotations[self::ARRAY_OF_TYPE_ANNOTATION];

                    if (empty($valueType)) {
                        throw new \InvalidArgumentException("The annotation '" . self::ARRAY_OF_TYPE_ANNOTATION . "' on '" . $setter->getName() . "' requires a parameter which defines the type of the elements in the array.");
                    }

                    $propertyValue = $array[$propertyKey];
                    if (!is_array($propertyValue)) {
                        $type = gettype($propertyValue);
                        throw new ObjectMappingException("The type for the property '$propertyName' (identified in array as '$propertyKey') is of invalid type, was '$type', expected '$propertyType'.");
                    }

                    $checkedArray = array();
                    foreach ($propertyValue as $key => $value) {
                        $checkedArray[] = $this->doMapArrayToType((array)$value, $valueType, "", $propertyKey . "[$key]");
                    }

                    $this->checkAnnotations($checkedArray, $annotations, $propertyKey, $parentKey);

                    $setter->invoke($objectToFill, $checkedArray);
                } catch (ObjectMappingException $e) {
                    if (!$this->failFastValidation && !empty($e->getValidationErrors())) {
                        $validationErrors = array_merge($validationErrors, $e->getValidationErrors());
                    } else {
                        throw $e;
                    }
                }

            } else { // primitive type
                try {
                    $propertyValue = $array[$propertyKey];

                    if (!empty($propertyType) && (!self::isOfValidType($propertyValue, $propertyType) && $propertyValue !== null)) {
                        $type = gettype($propertyValue);
                        throw new ObjectMappingException("The type for the property '$propertyName' (identified in array as '$propertyKey') is of invalid type, was '$type', expected '$propertyType'.");
                    }

                    if (!empty($propertyType)) {
                        $propertyValue = $this->coerceType($propertyValue, $propertyType);
                    }

                    $this->checkAnnotations($propertyValue, $annotations, $propertyKey, $parentKey);

                    $setter->invoke($objectToFill, $propertyValue);
                } catch (ObjectMappingException $e) {
                    if (!$this->failFastValidation && !empty($e->getValidationErrors())) {
                        $validationErrors = array_merge($validationErrors, $e->getValidationErrors());
                    } else {
                        throw $e;
                    }
                }
            }
        }

        if (!empty($validationErrors)) {
            throw new ObjectMappingException("Validation of the object failed, see validation errors", $validationErrors);
        }

        return $objectToFill;
    }

    private function coerceType($value, \ReflectionType $type)
    {
        switch ($type) {
            case "double":
            case "float":
                return (float)$value;
            case "int":
                return (int)$value;
            case "string":
                return (string)$value;
            case "array":
                return (array)$value;
            default:
                return $value;

        }
    }

    /**
     * Maps the provided object to an array or returns the provided object if it was of a built-in primitive type
     *
     * By using the 'arrayOf' annotation in the object's class for properties of type array the mapper will map these to
     * arrays recursively.
     *
     * @param mixed $object The object to map to an array
     * @param string $prefix with which the array keys should be prefixed i.e. the name of the object/variable passed
     * @return mixed either an array containing the properties of the object or the provided object if it had a built-in
     * primitive type
     */
    public function mapObjectToArray($object, string $prefix = "")
    {
        if (!is_object($object)) {
            return $object;
        }
        if (get_class($object) === \stdClass::class) {
            return (array)$object;
        }

        $objectClass = new \ReflectionClass($object);

        $getters = array_filter($objectClass->getMethods(\ReflectionMethod::IS_PUBLIC), "self::isGetter");

        $array = array();
        foreach ($getters as $getter) {
            $value = $getter->invoke($object);
            if ($value === null) {
                continue;
            }

            $propertyName = self::getPropertyName($getter);
            $propertyKey = empty($prefix) ? $propertyName : $prefix . self::PATH_DELIMITER . $propertyName;

            if (is_array($value) && self::hasArrayOfTypeAnnotation($objectClass, $getter)) {
                $items = array();
                foreach ($value as $index => $item) {
                    $items[] = $this->mapObjectToArray($item);
                }
                $array[$propertyKey] = $items;
            } else {
                $array[$propertyKey] = $this->mapObjectToArray($value);
            }
        }

        return $array;
    }

    private static function isOfValidType($value, $type)
    {
        // The gettype function will return double instead of float, however the type from reflection might come back as float.
        // See: http://php.net/manual/en/function.gettype.php
        $type = $type === "float" ? "double" : "" . $type;

        return gettype($value) === $type || self::isOfCoercibleType($value, $type);
    }

    private static function isOfCoercibleType($value, $type): bool
    {
        return (($type === "int" || $type === "double" || $type === "float") && is_numeric($value)) || ($type === "bool" && is_bool($value));
    }

    private static function isCustomType(\ReflectionType $propertyType)
    {
        return !empty($propertyType) && !$propertyType->isBuiltin();
    }

    private static function getPropertyType(\ReflectionMethod $setter)
    {
        return $setter->getParameters()[0]->getType();
    }

    private static function getPropertyName(\ReflectionMethod $setterOrGetter)
    {
        $methodName = $setterOrGetter->getName();
        return lcfirst(substr($methodName, 3));
    }

    private static function isSetter(\ReflectionMethod $method)
    {
        return StringHelper::startsWith($method->getName(), "set") && $method->getNumberOfParameters() === 1;
    }

    private static function isGetter(\ReflectionMethod $method)
    {
        return StringHelper::startsWith($method->getName(), "get") && $method->getNumberOfParameters() === 0;
    }

    private function checkAnnotations($propertyValue, $annotations, $propertyKey, $parentKey)
    {
        $validationErrors = array();
        foreach ($annotations as $name => $annotationValue) {
            if (self::isExcludedAnnotation($name)) {
                continue;
            }
            if (!array_key_exists($name, $this->validators)) {
                trigger_error("Ignoring annotation '$name' as there is no Validator registered that handles this annotation.", \E_USER_NOTICE);
                continue;
            }

            $validationErrorKey = empty($parentKey) ? $propertyKey : $parentKey . "[$propertyKey]";

            $validationError = $this->validators[$name]->validate($propertyValue, $annotationValue, $validationErrorKey);
            if (!empty($validationError)) {
                $validationErrors[] = $validationError;
            }
        }
        if (!empty($validationErrors)) {
            throw new ObjectMappingException("Validation of the object failed, see validation errors", $validationErrors);
        }
    }

    private static function isExcludedAnnotation($name)
    {
        if ($name === self::ARRAY_OF_TYPE_ANNOTATION || in_array($name, self::$PHP_DOC_ANNOTATIONS)) {
            return true;
        }

        return false;
    }

    private static function hasArrayOfTypeAnnotation(\ReflectionClass $objectClass, \ReflectionMethod $getter): bool
    {
        $setterName = str_replace("get", "set", $getter->getName());
        $annotationReader = new Reader($objectClass->getName(), $setterName);
        return $annotationReader->getParameter(self::ARRAY_OF_TYPE_ANNOTATION) !== null;
    }
}