<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class MaxValidationError implements IValidationError
{
    private $valueName;
    private $value;

    private $max;

    public function __construct($valueName, $value, int $max)
    {
        $this->valueName = $valueName;
        $this->value = $value;

        $this->max = $max;
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
     * Returns the max value which was exceeded by the MaxValidationError::value
     * @return int the max value that was exceeded
     */
    public function getMax(): int
    {
        return $this->max;
    }
}