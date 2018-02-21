<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class MinValidationError extends ValidationError
{

    private $min;

    public function __construct($valueName, $value, int $min)
    {
        parent::__construct($valueName, $value);

        $this->min = $min;
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