<?php
namespace RevisionPDO\Illuminate\Database\Capsule;

use Illuminate\Database\DatabaseManager;
use RevisionPDO\Illuminate\Database\Connectors\ConnectionFactory;

/**
 * @author Alwin Garside <alwin@garsi.de>
 * @property \Illuminate\Foundation\Application $container
 */
class Manager extends \Illuminate\Database\Capsule\Manager
{
    /**
     * Build the database manager instance.
     *
     * @return void
     */
    protected function setupManager()
    {
        $factory = new ConnectionFactory($this->container);

        $this->manager = new DatabaseManager($this->container, $factory);
    }
}
