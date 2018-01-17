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
    private $modifiers;
    private $setter;
    private $getter;
    private $annotations;

    public function __construct(string $name, string $type, $modifiers, $setter, $getter, array $annotations)
    {
        $this->name = $name;
        $this->type = $type;
        $this->modifiers = $modifiers;
        $this->setter = $setter;
        $this->getter = $getter;
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
    public function getModifiers()
    {
        return $this->modifiers;
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
    public function getGetter()
    {
        return $this->getter;
    }

    /**
     * @return mixed
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    public function isPublic() :bool {
        return in_array("public", $this->modifiers);
    }

    public function isProtected() :bool {
        return in_array("protected", $this->modifiers);
    }

    public function isPrivate() :bool {
        return in_array("private", $this->modifiers);
    }
}