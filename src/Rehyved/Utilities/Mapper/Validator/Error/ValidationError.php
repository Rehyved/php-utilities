<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;

class ValidationError
{

    protected $valueName;
    protected $value;

    protected function __construct(string $valueName, $value)
    {
        $this->valueName = $valueName;
        $this->value = $value;
    }

    /**
     * Returns the name with which the value can be identified in its source.
     * For example the key of the array entry which contained the value.
     * @return string
     */
    public function getValueName(): string{
        return $this->valueName;
    }

    /**
     * Returns the value of the validated field
     * @return mixed the value of the validated field
     */
    public function getValue(){
        return $this->value;
    }
}