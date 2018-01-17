<?php

namespace Rehyved\Utilities\Mapper;

use PHPUnit\Framework\TestCase;
use Rehyved\Utilities\Mapper\Validator\Error\IValidationError;
use Rehyved\Utilities\Mapper\Validator\MinValidator;
use Rehyved\Utilities\Mapper\Validator\RequiredValidator;

class User
{
    /**
     * @required
     */
    private $name;

    /**
     * @min 2
     * @var User[]
     */
    private $friends;

    /**
     * @required
     * @var int
     */
    private $age;

    /**
     * @var int
     */
    private $weight;

    protected $privacy;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->weight = 80;
    }

    /**
     * @required
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @min 2
     * @arrayOf Rehyved\Utilities\Mapper\User
     */
    public function setFriends($friends)
    {
        $this->friends = $friends;
    }

    public function getFriends()
    {
        return $this->friends;
    }

    /**
     * @return mixed
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @required
     * @param int $age
     */
    public function setAge(int $age)
    {
        $this->age = $age;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

}

class TestClass
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var User
     */
    private $user;

    /**
     * @var int[]
     */
    public $numbers;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getName()
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
    /**
     * Checks if the mapper can map from an array to an object, and back
     */
    public function testObjectMapper()
    {
        try {
            $testFriends = array(array("name" => "Test Friend 1", "age" => 25), array("name" => "Test Friend 2", "age" => "25"));
            $testArray = array(
                "test_name" => "Test 1",
                "test_user" => array(
                    "name" => "TestUser",
                    "friends" => $testFriends,
                    "age" => "25",
                    "privacy" => "should not break but skip this one"
                ),
                "test_numbers" => array(1,2,3,4,5)
            );

            $mapper = new ObjectMapper();

            $output = $mapper->mapArrayToObject($testArray, TestClass::class, "test");

            $this->assertEquals("Test 1", $output->getName());
            $this->assertEquals("TestUser", $output->getUser()->getName());
            $this->assertEquals(80, $output->getUser()->getWeight());
            $this->assertCount(count($testFriends), $output->getUser()->getFriends());
            $this->assertEquals($testFriends[0]["name"], $output->getUser()->getFriends()[0]->getName());
            $this->assertEquals($testFriends[1]["name"], $output->getUser()->getFriends()[1]->getName());
            $this->assertEquals($testArray["test_numbers"], $output->numbers);
        } catch (ObjectMappingException $e) {
            var_dump($e->getValidationErrors());
            throw $e;
        }
    }

    public function testToArrayMapping()
    {
        try {
            $testFriends = array(array("name" => "Test Friend 1", "age" => 25, "weight" => 80), array("name" => "Test Friend 2", "age" => "25", "weight" => 80));
            $testArray = array(
                "test_name" => "Test 1",
                "test_user" => array(
                    "name" => "TestUser",
                    "friends" => $testFriends,
                    "age" => "25",
                    "weight" => 80
                )
            );

            $mapper = new ObjectMapper();

            $output = $mapper->mapArrayToObject($testArray, TestClass::class, "test");

            $this->assertEquals($testArray, $mapper->mapObjectToArray($output, "test"));
        } catch (ObjectMappingException $e) {
            var_dump($e->getValidationErrors());
            throw $e;
        }
    }

    public function testValidatorIsTriggered()
    {
        $testArray = array(
            "test_name" => "Test 1",
            "test_user" => array(
                "name" => "TestUser",
                "friends" => array(),
                "age" => 25
            )
        );

        $mapper = new ObjectMapper();

        $this->expectException(ObjectMappingException::class);

        $mapper->mapArrayToObject($testArray, TestClass::class, "test");

    }
}
