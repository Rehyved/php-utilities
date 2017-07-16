<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class MinValidationError implements IValidationError
{
    private $valueName;
    private $value;

    private $min;

    public function __construct($valueName, $value, int $min)
    {
        $this->valueName = $valueName;
        $this->value = $value;

        $this->min = $min;
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
     * Returns the min value which was exceeded by the MinValidationError::value
     * @return int the min value that was exceeded
     */
    public function getMin(): int
    {
        return $this->min;
    }
}