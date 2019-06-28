<?php

namespace RevisionPDO;

class Operator
{
    /**
     * @var
     */
    public $name;

    /**
     * @var string|null
     */
    public $ipAddress;

    /**
     * @param string|null  $name
     * @param string|null  $ipAddress
     */
    public function __construct($name = null, $ipAddress = null)
    {
        if (null === $ipAddress) {
            $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        }

        $this->name      = $name;
        $this->ipAddress = $ipAddress;
    }
}