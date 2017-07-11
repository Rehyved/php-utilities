<?php
namespace Rehyved\Utilities\Mapper;

class ObjectMappingException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}