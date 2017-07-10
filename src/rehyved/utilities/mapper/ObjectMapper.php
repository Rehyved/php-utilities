<?php
namespace Rehyved\utilities\mapper;

use Doctrine\Common\Annotations\AnnotationReader;
use Rehyved\utilities\mapper\validator\IObjectMapperValidator;

class ObjectMapper
{
    private $validators = array();

    public function addValidator(IObjectMapperValidator $validator){
        $this->validators[$validator->getAnnotation()] = $validator;
    }

    /**
     * Map the provided array to an object of the provided type.
     * The prefix can be used to ignore certain values in the array.
     * 
     * NOTE:Arrays of custom objects are currently not deserialized and will remain an array.
     * 
     * @param array $array The array to fill the object with
     * @param string $type The type of object to map to
     * @param string $prefix The key prefix that should be used when determining the property values. Values in the array with different prefixes are ignored.
     */
    public function mapArrayToType(array $array, string $type, string $prefix = "")
    {
        $objectToFill = new $type();
        $reflectionClass = new \ReflectionClass($objectToFill);

        $setters = array_filter($reflectionClass->getMethods(), "self::isGetter");

        if (empty($setters)) {
            throw new ObjectMappingException("The provided class does not contain any setters.");
        }

        $setterInvoked = false;
        foreach ($setters as $setter) {

            $propertyName = self::getPropertyName($setter);

            $propertyType = self::getPropertyType($setter);

            if (self::isCustomType($propertyType)) {
                $customType = "" . $propertyType;
                $propertyValue = self::mapArrayToType($array, $customType, $prefix . "." . $propertyName);

                $setter->invoke($objectToFill, $propertyValue);
                $setterInvoked = true;
            }
            else { // primitive type


                $key = $prefix . "." . $propertyName;

                if (array_key_exists($key, $array) && !empty($array[$key])) {

                    $propertyValue = $array[$key];
                    if (!empty($propertyType) && !self::isOfValidType($propertyValue, $propertyType)) {
                        $type = gettype($propertyValue);
                        throw new ObjectMappingException("The type for the property '$propertyName' (identified in array as '$key') is of invalid type, was '$type', expected '$propertyType'.");
                    }
                    
                    $annotationReader = new \DocBlockReader\Reader($reflectionClass->getName(), $setter->getName());
                    $annotations = $annotationReader->getParameters();
                    
                    foreach($annotations as $name => $annotationValue){
                        if(!array_key_exists($name, $this->validators)){
                            trigger_error("Ignoring annotation '$name' as there is now validator registered that handles this annotation.", \E_USER_NOTICE);
                            continue;
                        }
                        $this->validators[$name]->validate($propertyValue, $annotationValue);
                    }

                    $setter->invoke($objectToFill, $propertyValue);
                    $setterInvoked = true;
                }
            }
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

    private static function isGetter(\ReflectionMethod $method)
    {
        return stripos($method->getName(), "set") === 0 && $method->getNumberOfParameters() === 1;
    }
}