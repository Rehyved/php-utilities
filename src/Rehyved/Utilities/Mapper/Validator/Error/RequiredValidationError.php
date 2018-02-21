<?php

namespace Rehyved\Utilities\Mapper\Validator\Error;


class RequiredValidationError extends ValidationError
{

    public function __construct($valueName, $value){
        parent::__construct($valueName, $value);
    }
}