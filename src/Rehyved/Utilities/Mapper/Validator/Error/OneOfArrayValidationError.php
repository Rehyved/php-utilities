<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class OneOfArrayValidationError extends ValidationError
{

    private $array;

    public function __construct($valueName, $value, array $array){
        parent::__construct($valueName, $value);

        $this->array = $array;
    }

    /**
     * Returns the array in which the OneOfArrayValidationError::value was not found
     * @return array the array in which the value was not found
     */
    public function getArray() : array {
        return $this->array;
    }
}