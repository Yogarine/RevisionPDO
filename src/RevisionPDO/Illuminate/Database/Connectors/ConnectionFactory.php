<?php
declare(strict_types=1);

namespace RevisionPDO\Illuminate\Database\Connectors;

use RevisionPDO\PDO;

/**
 * @author Alwin Garside <alwin@garsi.de>
 */
class ConnectionFactory extends \Illuminate\Database\Connectors\ConnectionFactory
{
    /**
     * Create a new Closure that resolves to a PDO instance.
     *
     * @param  array  $config
     * @return \Closure
     */
    protected function createPdoResolver(array $config)
    {
        $pdo = array_key_exists('host', $config)
            ? $this->createPdoResolverWithHosts($config)
            : $this->createPdoResolverWithoutHosts($config);

        return function () use ($pdo) {
            return new PDO($pdo());
        };
    }
}