<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class RequiredValidationError implements IValidationError
{
    private $valueName;
    private $value;

    public function __construct($valueName, $value){
        $this->valueName = $valueName;
        $this->value = $value;
    }

    public function getValueName(): string
    {
        return $this->valueName;
    }

    public function getValue()
    {
        return $this->value;
    }
}