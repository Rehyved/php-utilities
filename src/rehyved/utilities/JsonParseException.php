<?php

namespace Rehyved\utilities;


class JsonParseException extends \Exception
{

    private $json;
    public function __construct($message, $code, $json)
    {
        parent::__construct($message, $code);
        $this->json = $json;
    }

    /**
     * @return \Throwable
     */
    public function getJson(): \Throwable
    {
        return $this->json;
    }


}