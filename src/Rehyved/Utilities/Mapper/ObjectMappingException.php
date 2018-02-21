<?php
namespace Rehyved\Utilities\Mapper;

use Rehyved\Utilities\Mapper\Validator\Error\ValidationError;

class ObjectMappingException extends \Exception
{
    private $validationErrors;

    /**
     * ObjectMappingException constructor.
     * @param string $message
     * @param ValidationError[] $validationErrors
     */
    public function __construct(string $message, array $validationErrors = array() )
    {
        parent::__construct($message);
        $this->validationErrors = $validationErrors;
    }

    /**
     * @return ValidationError[]
     */
    public function getValidationErrors(){
        return $this->validationErrors;
    }
}