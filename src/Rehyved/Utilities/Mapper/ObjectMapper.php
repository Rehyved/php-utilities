<?php

namespace Rehyved\Utilities\Mapper;

use DocBlockReader\Reader;
use Rehyved\Utilities\Mapper\Validator\EmailAddressValidator;
use Rehyved\Utilities\Mapper\Validator\Error\TypeValidationError;
use Rehyved\Utilities\Mapper\validator\IObjectMapperValidator;
use Rehyved\Utilities\Mapper\Validator\MaxValidator;
use Rehyved\Utilities\Mapper\Validator\MinValidator;
use Rehyved\Utilities\Mapper\Validator\OneOfArrayValidator;
use Rehyved\Utilities\Mapper\Validator\RegexValidator;
use Rehyved\Utilities\Mapper\Validator\RequiredValidator;
use Rehyved\Utilities\Mapper\Validator\TypeValidator;
use Rehyved\Utilities\StringHelper;
use Rehyved\Utilities\TypeHelper;

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
        // "var", used for type annotation validation
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
        $this->addValidator(new TypeValidator());
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

    private static function isExcludedAnnotation($name)
    {
        if ($name === self::ARRAY_OF_TYPE_ANNOTATION || in_array($name, self::$PHP_DOC_ANNOTATIONS)) {
            return true;
        }

        return false;
    }

    /**
     * Maps the provided array to an instance of the provided type.
     * During mapping the validators are used to validate the parameters and values of the resulting object.
     * Based on the fail fast validation setting this function will fail immediately or at the end with the mapping run
     * with a list of validation errors.
     *
     * @param array $array The array containing the data to map to the object.
     * @param string $expectedType The type of the object of which an instance should be created and mapped to.
     * @param string $prefix The prefix used in the array for the keys of the object.
     * @return mixed An instance of the provided type.
     * @throws ObjectMappingException
     */
    public function mapArrayToObject(array $array, string $expectedType, string $prefix = "")
    {
        return $this->doMapArrayToType($array, $expectedType, $prefix, "");
    }

    /**
     * @throws ObjectMappingException
     */
    private function doMapArrayToType($valueToMap, string $expectedType, string $prefix, string $parentKey)
    {
        if ($valueToMap === null) {
            return null;
        }

        if (!is_array($valueToMap) && TypeHelper::isBuiltInType($expectedType)) {
            if (TypeHelper::isOfCoercibleType($valueToMap, $expectedType)) {
                return TypeHelper::coerceType($valueToMap, $expectedType);
            } else {
                $actualType = gettype($valueToMap);
                throw new ObjectMappingException(
                    "The type for the value '$valueToMap' is of invalid type, was '" . $actualType . "', expected '$expectedType'.",
                    array(new TypeValidationError($parentKey, $valueToMap, $expectedType, $actualType))
                );
            }
        } else if (!is_array($valueToMap) && !TypeHelper::isBuiltInType($expectedType) && get_class($valueToMap) === $expectedType) {
            return $valueToMap;
        }

        $array = (array)$valueToMap;

        $objectToFill = new $expectedType();
        $reflectionClass = new \ReflectionClass($objectToFill);

        $objectProperties = $this->getObjectProperties($reflectionClass);

        $validationErrors = array();
        foreach ($objectProperties as $property) {
            if ($property->getSetter() == null && !$property->isPublic()) {
                continue;
            }

            $propertyName = $property->getName();
            $propertyType = $property->getType();
            $annotations = $property->getAnnotations();

            $propertyKey = empty($prefix) ? $propertyName : $prefix . self::PATH_DELIMITER . $propertyName;

            try {
                $propertyValue = null;

                if (array_key_exists($propertyKey, $array)) {
                    $arrayValue = $array[$propertyKey];

                    if (array_key_exists(TypeValidator::ANNOTATION, $annotations) && TypeHelper::isArrayType($annotations[TypeValidator::ANNOTATION])) {
                        $valueType = $annotations[TypeValidator::ANNOTATION];

                        if (empty($valueType)) {
                            throw new \InvalidArgumentException("The annotation '" . self::ARRAY_OF_TYPE_ANNOTATION . "' on '$propertyName' requires a parameter which defines the type of the elements in the array.");
                        }

                        if ($arrayValue !== null) {
                            if (!is_array($arrayValue)) {
                                $expectedType = gettype($arrayValue);
                                throw new ObjectMappingException("The type for the property '$propertyName' (identified in array as '$propertyKey') is of invalid type, was '$expectedType', expected '" . $propertyType . "'.", array(new TypeValidationError($propertyName, $arrayValue)));
                            }

                            $valueType = substr($valueType, 0, strlen($valueType) - 2);
                            $valueType = TypeHelper::isBuiltInType($valueType) || class_exists($valueType) ? $valueType : $reflectionClass->getNamespaceName() . "\\" . $valueType;

                            $propertyValue = array();
                            foreach ($arrayValue as $key => $value) {
                                $propertyValue[] = $this->doMapArrayToType($value, $valueType, "", $propertyKey . "[$key]");
                            }
                        }
                    } else {

                        $propertyValue = $this->doMapArrayToType($arrayValue, $propertyType, "", empty($parentKey) ? $propertyKey : $parentKey . "[$propertyKey]");
                    }
                }

                $this->checkAnnotations($propertyValue, $annotations, $propertyKey, $parentKey);

                if (array_key_exists($propertyKey, $array)) {
                    $this->setValue($objectToFill, $property, $propertyValue);
                    //$setter->invoke($objectToFill, $propertyValue);
                }
            } catch (ObjectMappingException $e) {
                if (!$this->failFastValidation && !empty($e->getValidationErrors())) {
                    $validationErrors = array_merge($validationErrors, $e->getValidationErrors());
                } else {
                    throw $e;
                }
            }

        }

        if (!empty($validationErrors)) {
            throw new ObjectMappingException("Validation of the object failed, see validation errors", $validationErrors);
        }

        return $objectToFill;
    }

    private function getObjectProperties(\ReflectionClass $reflectionClass): array
    {
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PUBLIC);

        return array_map(function ($property) use ($reflectionClass) {
            return self::toObjectProperty($property, $reflectionClass);
        }, $properties);
    }

    public function toObjectProperty(\ReflectionProperty $reflectionProperty, \ReflectionClass $reflectionClass): ObjectProperty
    {
        $propertyName = $reflectionProperty->getName();
        $annotationReader = new Reader($reflectionClass->getName(), $reflectionProperty->getName(), "property");
        $annotations = $annotationReader->getParameters();
        $propertyType = "mixed";

        if (array_key_exists(TypeValidator::ANNOTATION, $annotations)) {
            $propertyType = $annotations[TypeValidator::ANNOTATION];
        }

        if (StringHelper::endsWith($propertyType, "[]")) {
            $valueType = substr($propertyType, 0, strlen($propertyType) - 2);
            $valueType = TypeHelper::isBuiltInType($valueType) || class_exists($valueType) ? $valueType : $reflectionClass->getNamespaceName() . "\\" . $valueType;
            $annotations[TypeValidator::ANNOTATION] = $valueType . "[]";

            $propertyType = "array";
        } else if (!TypeHelper::isBuiltInType($propertyType) && !class_exists($propertyType)) {
            $propertyType = $reflectionClass->getNamespaceName() . "\\" . $propertyType;
            $annotations[TypeValidator::ANNOTATION] = $propertyType;
        }

        $propertyModifiers = \Reflection::getModifierNames($reflectionProperty->getModifiers());

        $propertySetter = null;
        try {
            $propertySetter = $reflectionClass->getMethod("set" . ucfirst($propertyName));
        } catch (\ReflectionException $e) {
            // This is permitted
        }

        $propertyGetter = null;
        try {
            $propertyGetter = $reflectionClass->getMethod("get" . ucfirst($propertyName));
        } catch (\ReflectionException $e) {
            // This is permitted
        }
        return new ObjectProperty($propertyName, $propertyType, $propertyModifiers, $propertySetter, $propertyGetter, $annotations);
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

        $objectProperties = $this->getObjectProperties($objectClass);

        $array = array();
        foreach ($objectProperties as $property) {
            if ($property->getGetter() == null && !$property->isPublic()) {
                continue;
            }

            $value = $this->getValue($object, $property);
            if ($value === null) {
                continue;
            }

            $propertyName = $property->getName();
            $annotations = $property->getAnnotations();

            $propertyKey = empty($prefix) ? $propertyName : $prefix . self::PATH_DELIMITER . $propertyName;

            if (is_array($value)
                && array_key_exists(TypeValidator::ANNOTATION, $annotations)
                && TypeHelper::isArrayType($annotations[TypeValidator::ANNOTATION])
            ) {
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

    /**
     * @throws ObjectMappingException
     */
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

    private function setValue($object, ObjectProperty $property, $propertyValue)
    {
        if ($property->getSetter() !== null) {
            $property->getSetter()->invoke($object, $propertyValue);
        } else {
            $propertyName = $property->getName();
            $object->$propertyName = $propertyValue;
        }
    }

    private function getValue($object, ObjectProperty $property)
    {
        if ($property->getGetter() !== null) {
            return $property->getGetter()->invoke($object);
        } else {
            $propertyName = $property->getName();
            return $object->$propertyName;
        }
    }
}