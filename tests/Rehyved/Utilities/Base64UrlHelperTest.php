<?php

namespace Rehyved\Utilities;


use PHPUnit\Framework\TestCase;

class Base64UrlHelperTest extends TestCase
{
    public function testUrlHelperEncode() {
        $testString = "ðáßðßáßð";
        $testString2 = "g║";
        $this->assertEquals("w7DDocOfw7DDn8Ohw5_DsA", Base64UrlHelper::encode($testString));
        $this->assertEquals("Z-KVkQ", Base64UrlHelper::encode($testString2));
    }

    public function testUrlHelperDecode() {
        $testString = "w7DDocOfw7DDn8Ohw5_DsA";
        $testString2 = "Z-KVkQ";
        $this->assertEquals("ðáßðßáßð", Base64UrlHelper::decode($testString));
        $this->assertEquals("g║", Base64UrlHelper::decode($testString2));
    }
}