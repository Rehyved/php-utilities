<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class RegexValidationError implements IValidationError
{
    private $valueName;
    private $value;

    private $regex;

    public function __construct($valueName, $value, string $regex){
        $this->valueName = $valueName;
        $this->value = $value;

        $this->regex = $regex;
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
     * Returns the regex which the OneOfArrayValidationError::value did not match
     * @return string the regex that was not matched
     */
    public function getRegex() : string {
        return $this->regex;
    }
}