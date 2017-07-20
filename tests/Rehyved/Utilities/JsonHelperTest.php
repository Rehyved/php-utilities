<?php

namespace Rehyved\Utilities;


use PHPUnit\Framework\TestCase;

class JsonHelperTest extends TestCase
{
    public function testValidJsonParses(){
        $json = '{"test" : "value"}';
        $parsed = JsonHelper::tryParse($json);

        $this->assertEquals($parsed, json_decode($json));
    }

    public function testInvalidJsonThrowsException(){
        $this->expectException(JsonParseException::class);
        $invalidJson = '{"test" : "value"';
        JsonHelper::tryParse($invalidJson);
    }
}