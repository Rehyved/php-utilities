<?php
namespace Rehyved\Utilities\Mapper;

class ObjectMappingException extends \Exception
{
    private $validationErrors;

    public function __construct($message, array $validationErrors = array() )
    {
        parent::__construct($message);
        $this->validationErrors = $validationErrors;
    }

    public function getValidationErrors(){
        return $this->validationErrors;
    }
}