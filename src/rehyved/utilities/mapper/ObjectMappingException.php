<?php
namespace Rehyved\utilities\mapper;

class ObjectMappingException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}