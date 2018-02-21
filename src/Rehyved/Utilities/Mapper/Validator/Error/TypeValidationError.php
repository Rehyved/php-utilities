<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class TypeValidationError extends ValidationError
{
    private $expectedType;
    private $actualType;

    public function __construct($valueName, $value, string $expectedType, string $actualType){
        parent::__construct($valueName, $value);

        $this->expectedType = $expectedType;
        $this->actualType = $actualType;
    }

    /**
     * Returns the type that was expected in the mapping.
     * @return string expected type
     */
    public function getExpectedType() : string
    {
        return $this->expectedType;
    }

    /**
     * Returns the actual type that was encountered during the mapping
     * @return string actual type
     */
    public function getActualType() :string
    {
        return $this->actualType;
    }


}