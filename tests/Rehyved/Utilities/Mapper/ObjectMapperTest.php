<?php

namespace Rehyved\Utilities\Mapper;

use PHPUnit\Framework\TestCase;
use Rehyved\Utilities\Mapper\Validator\MinValidator;
use Rehyved\Utilities\Mapper\Validator\RequiredValidator;

class User
{
    private $name;
    private $friends;

    /**
     * @required
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @min 2
     * @arrayOf Rehyved\Utilities\Mapper\User
     */
    public function setFriends(array $friends)
    {
        $this->friends = $friends;
    }

    public function getFriends(): array
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getUser()
    {
        return $this->user;
    }
}

class ObjectMapperTest extends TestCase
{
    public function testObjectMapper()
    {
        $testFriends = array(array("name" => "Test Friend 1"), array("name" => "Test Friend 2"));
        $testArray = array(
            "test_name" => "Test 1",
            "name" => "Wrong name",
            "test_user_name" => "TestUser",
            "test_user_friends" => $testFriends
        );

        $mapper = new ObjectMapper();
        $mapper->addValidator(new MinValidator());
        $mapper->addValidator(new RequiredValidator());
        $output = $mapper->mapArrayToType($testArray, TestClass::class, "test");

        $this->assertEquals("Test 1", $output->getName());
        $this->assertEquals("TestUser", $output->getUser()->getName());
        $this->assertCount(count($testFriends), $output->getUser()->getFriends());
        $this->assertEquals($testFriends[0]["name"], $output->getUser()->getFriends()[0]->getName());
        $this->assertEquals($testFriends[1]["name"], $output->getUser()->getFriends()[1]->getName());

    }
}
