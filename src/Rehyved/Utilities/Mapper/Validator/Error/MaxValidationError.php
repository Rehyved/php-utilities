<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class MaxValidationError extends ValidationError
{
    private $max;

    public function __construct($valueName, $value, int $max)
    {
        parent::__construct($valueName, $value);

        $this->max = $max;
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