<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class OneOfArrayValidationError implements IValidationError
{
    private $valueName;
    private $value;

    private $array;

    public function __construct($valueName, $value, array $array){
        $this->valueName = $valueName;
        $this->value = $value;

        $this->array = $array;
    }

    public function getValueName(): string
    {
        return $this->valueName;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * Returns the array in which the OneOfArrayValidationError::value was not found
     * @return array the array in which the value was not found
     */
    public function getArray() : array {
        return $this->array;
    }
}