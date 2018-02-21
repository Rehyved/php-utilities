<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class RegexValidationError extends ValidationError
{
    private $regex;

    public function __construct($valueName, $value, string $regex){
        parent::__construct($valueName, $value);

        $this->regex = $regex;
    }

    /**
     * Returns the regex which the OneOfArrayValidationError::value did not match
     * @return string the regex that was not matched
     */
    public function getRegex() : string {
        return $this->regex;
    }
}