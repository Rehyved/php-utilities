<?php

namespace Rehyved\Utilities;


use PHPUnit\Framework\TestCase;

class Base64UrlTest extends TestCase
{
    public function testEncode() {
        $testString = "ðáßðßáßð";
        $testString2 = "g║";
        $this->assertEquals("w7DDocOfw7DDn8Ohw5_DsA", Base64Url::encode($testString));
        $this->assertEquals("Z-KVkQ", Base64Url::encode($testString2));
    }

    public function testDecode() {
        $testString = "w7DDocOfw7DDn8Ohw5_DsA";
        $testString2 = "Z-KVkQ";
        $this->assertEquals("ðáßðßáßð", Base64Url::decode($testString));
        $this->assertEquals("g║", Base64Url::decode($testString2));
    }
}