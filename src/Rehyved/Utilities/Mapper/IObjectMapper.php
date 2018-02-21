<?php

namespace Rehyved\Utilities\Mapper;

use Rehyved\Utilities\Mapper\validator\IObjectMapperValidator;

interface IObjectMapper
{
    /**
     * Adds the provided IObjectMapperValidator to the set of validators used while mapping classes.
     * @see IObjectMapper::mapArrayToObject()
     * @param IObjectMapperValidator $validator
     * @return mixed
     */
    public function addValidator(IObjectMapperValidator $validator);

    /**
     * Map the provided array to an object of the provided type.
     * The prefix can be used to ignore certain values in the array or if the array contains multiple objects and needs
     * to be filtered.
     *
     * By using annotations handled by the registered IObjectMapperValidator validators more validation outside of type
     * checks can be performed.
     *
     * By using the 'arrayOf' annotation in the provided type's class for properties of type array the mapper will try
     * to map this sub type recursively from the provided array.
     *
     * @param array $array The array to fill the object with
     * @param string $expectedType The type of object to map to
     * @param string $prefix The key prefix that should be used when determining the property values. Values in the
     * array with different prefixes are ignored.
     * @return mixed object of the provided type containing the values taken from the provided array
     * @throws ObjectMappingException when there was an error while validating the type of the provided values in the
     * array or if there was a failed validation of one of the IObjectMapperValidator validators.
     */
    public function mapArrayToObject(array $array, string $expectedType, string $prefix = "");


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
    public function mapObjectToArray($object, string $prefix = "");
}