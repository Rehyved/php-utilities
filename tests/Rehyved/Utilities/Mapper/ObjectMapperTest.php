<?php
namespace Rehyved\Utilities\Mapper;

use PHPUnit\Framework\TestCase;

class User
{
    private $name;
    private $friends;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }

    /**
    * @min 2
    */
    public function setFriends(array $friends)
    {
        $this->friends = $friends;
    }

    public function getFriends() : array
    {
        return $this->friends;
    }
}

class TestClass
{
    private $name;
    private $user;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getUser()
    {
        return $this->user;
    }
}

class MinValidator implements \Rehyved\Utilities\Mapper\validator\IObjectMapperValidator
{
    public function getAnnotation() : string
    {
        return "min";
    }

    public function validate($value, $annotationParameter)
    {
        if (is_array($value) && count($value) < $annotationParameter) {
            throw new \Exception();
        } elseif (is_string($value) && \mb_strlen($value) < $annotationParameter) {
            throw new \Exception();
        } elseif (\is_numeric($value) && $value < $annotationParameter) {
            throw new \Exception();
        }
    }
}

class ObjectMapperTest extends TestCase
{
    public function testObjectMapper()
    {
        $testFriends = array("TestUser2","TestUser3");
        $testArray = array(
            "test.name" => "Test 1",
            "name" => "Wrong name",
            "test.user.name" => "TestUser",
            "test.user.friends" => $testFriends
        );

        $mapper = new ObjectMapper();
        $mapper->addValidator(new MinValidator());
        $output = $mapper->mapArrayToType($testArray, TestClass::class, "test");
        $this->assertEquals("Test 1", $output->getName());
        $this->assertEquals("TestUser", $output->getUser()->getName());
        $this->assertEquals($testFriends, $output->getUser()->getFriends());
    }
}
