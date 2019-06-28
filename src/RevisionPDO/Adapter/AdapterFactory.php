<?php
namespace RevisionPDO\Adapter;

use PDO;
use PHPSQLParser\Options;
use PHPSQLParser\PHPSQLParser;
use RevisionPDO\Operator;

class AdapterFactory
{
    /**
     * @param  \PDO                             $pdo
     * @param  \RevisionPDO\Operator            $operator
     * @return \RevisionPDO\Adapter\DefaultAdapter
     */
    public function make(PDO $pdo, Operator $operator)
    {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        switch ($driver) {
            case 'mysql':
                return new MySqlAdapter($pdo, $operator, new PHPSQLParser);

            case 'pgsql':
                return new PostgresAdapter(
                    $pdo,
                    $operator,
                    new PHPSQLParser(false, false, array(Options::ANSI_QUOTES => true))
                );

            default:
                return new DefaultAdapter(
                    $pdo,
                    $operator,
                    new PHPSQLParser(false, false, array(Options::ANSI_QUOTES => true))
                );
        }
    }
}
