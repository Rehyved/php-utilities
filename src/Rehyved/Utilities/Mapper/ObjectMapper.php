<?php

namespace Rehyved\Utilities\Mapper;

use DocBlockReader\Reader;
use Rehyved\Utilities\Mapper\validator\IObjectMapperValidator;

class ObjectMapper implements IObjectMapper
{
    const ARRAY_OF_TYPE_ANNOTATION = "arrayOf";
    const PATH_DELIMITER = "_";

    private $validators = array();

    private $failFastValidation;

    public function addValidator(IObjectMapperValidator $validator, $failFastValidation = false)
    {
        if ($validator->getAnnotation() === self::ARRAY_OF_TYPE_ANNOTATION) {
            throw new \InvalidArgumentException(
                "Cannot add a validator with the name "
                . self::ARRAY_OF_TYPE_ANNOTATION
                . " this annotation name is used for indicating array object types. Choose a different "
                . "annotation name for this validator."
            );
        }
        $this->validators[$validator->getAnnotation()] = $validator;
        $this->failFastValidation = $failFastValidation;
    }

    public function mapArrayToType(array $array, string $type, string $prefix = "")
    {
        return $this->doMapArrayToType($array, $type, $prefix, "");
    }

    private function doMapArrayToType(array $array, string $type, string $prefix, string $parentKey)
    {
        $objectToFill = new $type();
        $reflectionClass = new \ReflectionClass($objectToFill);

        $setters = array_filter($reflectionClass->getMethods(), "self::isSetter");

        if (empty($setters)) {
            throw new ObjectMappingException("The provided class does not contain any setters.");
        }

        $setterInvoked = false;
        $validationErrors = array();
        foreach ($setters as $setter) {

            $propertyName = self::getPropertyName($setter);

            $propertyType = self::getPropertyType($setter);
            $propertyKey = empty($prefix) ? $propertyName : $prefix . self::PATH_DELIMITER . $propertyName;

            $annotationReader = new Reader($reflectionClass->getName(), $setter->getName());
            $annotations = $annotationReader->getParameters();

            if (self::isCustomType($propertyType)) {
                try {
                    $customType = "" . $propertyType;
                    $propertyValue = $this->doMapArrayToType($array, $customType, $propertyKey, "");

                    $this->checkAnnotations($propertyValue, $annotations, $propertyKey, $parentKey);

                    $setter->invoke($objectToFill, $propertyValue);
                    $setterInvoked = true;
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
                    if (array_key_exists($propertyKey, $array)) {
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
                            $checkedArray[] = $this->doMapArrayToType($value, $valueType, "", $propertyKey . "[$key]");
                        }
                    }

                    $this->checkAnnotations($checkedArray, $annotations, $propertyKey, $parentKey);

                    if ($checkedArray != null) {
                        $setter->invoke($objectToFill, $checkedArray);
                        $setterInvoked = true;
                    }
                } catch (ObjectMappingException $e) {
                    if (!$this->failFastValidation && !empty($e->getValidationErrors())) {
                        $validationErrors = array_merge($validationErrors, $e->getValidationErrors());
                    } else {
                        throw $e;
                    }
                }

            } else { // primitive type
                try {
                    $propertyValue = array_key_exists($propertyKey, $array) ? $array[$propertyKey] : null;
                    if (!empty($propertyType) && (!self::isOfValidType($propertyValue, $propertyType) && !is_null($propertyValue))) {
                        $type = gettype($propertyValue);
                        throw new ObjectMappingException("The type for the property '$propertyName' (identified in array as '$propertyKey') is of invalid type, was '$type', expected '$propertyType'.");
                    }

                    $this->checkAnnotations($propertyValue, $annotations, $propertyKey, $parentKey);

                    $setter->invoke($objectToFill, $propertyValue);
                    $setterInvoked = true;
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

        return $setterInvoked ? $objectToFill : null;
    }

    private static function isOfValidType($value, $type)
    {
        // The gettype function will return double instead of float, however the type from reflection might come back as float.
        // See: http://php.net/manual/en/function.gettype.php
        $type = $type === "float" ? "double" : "" . $type;
        return gettype($value) === $type;
    }

    private static function isCustomType(\ReflectionType $propertyType)
    {
        return !empty($propertyType) && !$propertyType->isBuiltin();
    }

    private static function getPropertyType(\ReflectionMethod $setter)
    {
        return $setter->getParameters()[0]->getType();
    }

    private static function getPropertyName(\ReflectionMethod $setter)
    {
        $setterName = $setter->getName();
        return strtoLower(substr($setterName, strlen("set")));
    }

    private static function isSetter(\ReflectionMethod $method)
    {
        return stripos($method->getName(), "set") === 0 && $method->getNumberOfParameters() === 1;
    }

    private function checkAnnotations($propertyValue, $annotations, $propertyKey, $parentKey)
    {
        $validationErrors = array();
        foreach ($annotations as $name => $annotationValue) {
            if ($name === self::ARRAY_OF_TYPE_ANNOTATION) {
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
}