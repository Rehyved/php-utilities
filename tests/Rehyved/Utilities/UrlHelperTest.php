<?php

namespace Rehyved\Utilities;


use PHPUnit\Framework\TestCase;

class UrlHelperTest extends TestCase
{
    private $httpUrl = "http://www.localhost:8080/test/path?var1=val1&var2=val2#anchor";
    private $httpsUrl = "https://www.localhost:8080/test/path?var1=val1&var2=val2#anchor";

    public function testGetHostname()
    {
        $this->assertEquals("www.localhost", UrlHelper::getHostname($this->httpUrl));
    }

    public function testGetPort()
    {
        $this->assertEquals(8080, UrlHelper::getPort($this->httpUrl));
        $this->assertEquals(1, UrlHelper::getPort("http://www.test2:1"));
    }

    public function testGetAuthority()
    {
        $this->assertEquals("www.localhost:8080", UrlHelper::getAuthority($this->httpUrl));
    }

    public function testIsHttp()
    {
        $this->assertTrue(UrlHelper::isHttp($this->httpUrl));
        $this->assertFalse(UrlHelper::isHttp($this->httpsUrl));
    }

    public function testIsHttps()
    {
        $this->assertTrue(UrlHelper::isHttps($this->httpsUrl));
        $this->assertFalse(UrlHelper::isHttps($this->httpUrl));
    }

    public function testIsValidUrl()
    {
        $this->assertTrue(UrlHelper::isValidUrl($this->httpUrl));
        $this->assertTrue(UrlHelper::isValidUrl($this->httpsUrl));

        $this->assertFalse(UrlHelper::isValidUrl("-"));
    }

    public function testValidateUrl()
    {
        $this->assertEquals($this->httpUrl, UrlHelper::validateUrl($this->httpUrl));
        $this->assertEquals($this->httpsUrl, UrlHelper::validateUrl($this->httpsUrl));

        $this->expectException(\InvalidArgumentException::class);
        UrlHelper::validateUrl("-");
    }

    public function testRemoveTrailingSlash()
    {
        $testUrl = "http://localhost/";

        $this->assertEquals(substr($testUrl, 0, strlen($testUrl) - 1), UrlHelper::removeTrailingSlash($testUrl));
    }

    public function testEnsureTrailingSlash()
    {
        $testUrl = "http://localhost";
        $this->assertEquals($testUrl . "/", UrlHelper::ensureTrailingSlash($testUrl));
        $this->assertEquals($testUrl . "/", UrlHelper::ensureTrailingSlash($testUrl . "/"));
    }

    public function testBuildUrl()
    {
        $testUrl = "http://localhost";

        // Test using default encoding of PHP_QUERY_RFC3986 causing spaces to become %20
        $builtUrl = UrlHelper::buildUrl($testUrl, array("var1" => "val1", "var2" => "val 2"));
        $this->assertEquals($testUrl . "?var1=val1&var2=val%202", $builtUrl);

        // Test using encoding PHP_QUERY_RFC1738 causing spaces to become +
        $builtUrl = UrlHelper::buildUrl($testUrl, array("var1" => "val1", "var2" => "val 2"), PHP_QUERY_RFC1738);
        $this->assertEquals($testUrl . "?var1=val1&var2=val+2", $builtUrl);
    }
}