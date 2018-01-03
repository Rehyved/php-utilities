<?php
/**
 * Created by Rehyved.
 * User: M.P. Waldhorst
 * Date: 1/3/2018
 * Time: 11:33 PM
 */

namespace Rehyved\Utilities\Mapper;


class ObjectProperty
{
    private $propertyName;
    private $propertyType;
    private $propertySetter;
    private $annotations;

    public function __construct(string $propertyName, string $propertyType, $propertySetter, array $annotations)
    {
        $this->propertyName = $propertyName;
        $this->propertyType = $propertyType;
        $this->propertySetter = $propertySetter;
        $this->annotations = $annotations;
    }

    /**
     * @return mixed
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @return mixed
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * @return mixed
     */
    public function getPropertySetter()
    {
        return $this->propertySetter;
    }

    /**
     * @return mixed
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }


}