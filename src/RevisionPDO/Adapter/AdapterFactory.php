<?php
namespace RevisionPDO\Adapter;

use PDO;
use PHPSQLParser\PHPSQLParser;

class AdapterFactory
{
    /**
     * @param  \PDO  $pdo
     * @return \RevisionPDO\Adapter\DefaultAdapter
     */
    public function make($pdo)
    {
        $driver    = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $sqlParser = new PHPSQLParser();

        switch ($driver) {
            case 'mysql':
                return new MySqlAdapter($pdo, $sqlParser);

            case 'pgsql':
                return new PostgresAdapter($pdo, $sqlParser);

            default:
                return new DefaultAdapter($pdo, $sqlParser);
        }
    }
}
