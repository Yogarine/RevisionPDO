<?php
namespace RevisionPDO;

/**
 * @author Alwin Garside <alwin@garsi.de>
 */
trait WrapperTrait
{
    /** @var \PDO */
    protected $wrappee;

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->wrappee, $name], $arguments);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->wrappee->$name;
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return mixed
     */
    public function __set($name, $value)
    {
        $this->wrappee->$name = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->wrappee->$name);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->wrappee->$name);
    }
}