<?php

namespace Rehyved\Utilities;


use PHPUnit\Framework\TestCase;
use Rehyved\Utilities\StringHelper;

class StringHelperTest extends TestCase
{
    public function testEndsWithCaseSensitive()
    {
        $testString = "testString";

        $this->assertTrue(StringHelper::endsWith($testString, "String"));
        $this->assertFalse(StringHelper::endsWith($testString, "string"));
        $this->assertFalse(StringHelper::endsWith($testString, "garbage"));
        $this->assertFalse(StringHelper::endsWith($testString, "test"));
    }

    public function testEndsWithCaseInsensitive()
    {
        $testString = "testString";

        $this->assertTrue(StringHelper::endsWith($testString, "String"), true);
        $this->assertTrue(StringHelper::endsWith($testString, "string", true));
        $this->assertFalse(StringHelper::endsWith($testString, "garbage", true));
        $this->assertFalse(StringHelper::endsWith($testString, "test", true));
    }

    public function testEndsWithWithMultiByteCharacters(){
        $testString = "testStrïng";

        $this->assertTrue(StringHelper::endsWith($testString, "Strïng"));
        $this->assertTrue(StringHelper::endsWith($testString, "strïng", true));
    }

    public function testStartsWithCaseSensitive()
    {
        $testString = "testString";

        $this->assertTrue(StringHelper::startsWith($testString, "test"));
        $this->assertFalse(StringHelper::startsWith($testString, "Test"));
        $this->assertFalse(StringHelper::startsWith($testString, "garbage"));
        $this->assertFalse(StringHelper::startsWith($testString, "String"));
    }

    public function testStartsWithCaseInsensitive()
    {
        $testString = "testString";

        $this->assertTrue(StringHelper::startsWith($testString, "test"));
        $this->assertTrue(StringHelper::startsWith($testString, "Test", true));
        $this->assertFalse(StringHelper::startsWith($testString, "garbage", true));
        $this->assertFalse(StringHelper::startsWith($testString, "String", true));
    }

    public function testStartsWithWithMultiByteCharacters(){
        $testString = "tëstString";

        $this->assertTrue(StringHelper::startsWith($testString, "tëst"));
        $this->assertTrue(StringHelper::startsWith($testString, "Tëst", true));
    }

    public function testEqualsCaseSensitive()
    {
        $testString = "testString";

        $this->assertTrue(StringHelper::equals($testString, $testString));
        $this->assertFalse(StringHelper::equals($testString, strtoupper($testString)));
        $this->assertFalse(StringHelper::equals($testString, "garbage"));
    }

    public function testEqualsCaseInsensitive()
    {
        $testString = "testString";

        $this->assertTrue(StringHelper::equals($testString, $testString), true);
        $this->assertTrue(StringHelper::equals($testString, strtoupper($testString), true));
        $this->assertFalse(StringHelper::equals($testString, "garbage", true));
    }

    public function testEqualsWithMultiByteCharacters(){
        $testString = "tëstStrïng";

        $this->assertTrue(StringHelper::equals($testString, $testString));
        $this->assertTrue(StringHelper::equals($testString, strtoupper($testString), true));
    }

    public function testContainsCaseSensitive()
    {
        $testString = "testString";

        $this->assertTrue(StringHelper::contains($testString, $testString));
        $this->assertTrue(StringHelper::contains($testString, substr($testString, 2, strlen($testString) - 2)));
        $this->assertFalse(StringHelper::contains($testString, strtoupper(substr($testString, 2, strlen($testString) - 2))));
        $this->assertFalse(StringHelper::contains($testString, "garbage"));
        $this->assertFalse(StringHelper::contains($testString, $testString . "garbage"));
    }

    public function testContainsCaseInsensitive()
    {
        $testString = "testString";

        $this->assertTrue(StringHelper::contains($testString, $testString, true));
        $this->assertTrue(StringHelper::contains($testString, substr($testString, 2, strlen($testString) - 2), true));
        $this->assertTrue(StringHelper::contains($testString, strtoupper(substr($testString, 2, strlen($testString) - 2)), true));
        $this->assertFalse(StringHelper::contains($testString, "garbage", true));
        $this->assertFalse(StringHelper::contains($testString, $testString . "garbage", true));
    }

    public function testContainsWithMultiByteCharacters(){
        $testString = "tëstStrïng";

        $this->assertTrue(StringHelper::contains($testString, substr($testString, 2, strlen($testString) - 2)));
        $this->assertTrue(StringHelper::contains($testString, strtoupper(substr($testString, 2, strlen($testString) - 2)), true));
    }
}