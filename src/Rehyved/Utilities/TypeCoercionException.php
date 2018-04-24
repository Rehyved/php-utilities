<?php

namespace Rehyved\Utilities;


class TypeCoercionException extends \Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}