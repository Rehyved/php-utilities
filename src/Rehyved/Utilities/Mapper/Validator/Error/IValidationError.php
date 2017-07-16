<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;

interface IValidationError
{
    /**
     * Returns the name with which the value can be identified in its source.
     * For example the key of the array entry which contained the value.
     * @return string
     */
    public function getValueName(): string;

    /**
     * Returns the value of the validated field
     * @return mixed the value of the validated field
     */
    public function getValue();
}