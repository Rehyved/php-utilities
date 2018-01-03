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
    private $name;
    private $type;
    private $setter;
    private $annotations;

    public function __construct(string $name, string $type, $setter, array $annotations)
    {
        $this->name = $name;
        $this->type = $type;
        $this->setter = $setter;
        $this->annotations = $annotations;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getSetter()
    {
        return $this->setter;
    }

    /**
     * @return mixed
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }


}