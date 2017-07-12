<?php

namespace Rehyved\Utilities\Mapper;

use Rehyved\Utilities\Mapper\validator\IObjectMapperValidator;

interface IObjectMapper
{
    /**
     * Adds the provided IObjectMapperValidator to the set of validators used while mapping classes.
     * @see IObjectMapper::mapArrayToType()
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
     * checks can be performed
     *
     * NOTE:Arrays of custom objects are currently not deserialized and will remain an array.
     *
     * @param array $array The array to fill the object with
     * @param string $type The type of object to map to
     * @param string $prefix The key prefix that should be used when determining the property values. Values in the
     * array with different prefixes are ignored.
     * @return mixed object of the provided type containing the values taken from the provided array
     * @throws ObjectMappingException when there was an error while validating the type of the provided values in the
     * array or if there was a failed validation of one of the IObjectMapperValidator validators.
     */
    public function mapArrayToType(array $array, string $type, string $prefix = "");
}