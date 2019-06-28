<?php

namespace RevisionPDO\Adapter;

use PDO;
use PHPSQLParser\PHPSQLParser;
use RevisionPDO\Operator;

class PostgresAdapter extends DefaultAdapter
{
//    /**
//     * @param  \PDO                        $pdo
//     * @param  \RevisionPDO\Operator       $operator
//     * @param  \PHPSQLParser\PHPSQLParser  $sqlParser
//     */
//    public function __construct(PDO $pdo, Operator $operator, PHPSQLParser $sqlParser)
//    {
//        $sqlParse
//        parent($pdo, $operator, $sqlParser);
//    }

    /**
     * @param  string  $timezone
     */
    public function setTimeZone($timezone)
    {
        $this->pdo->exec("SET TIME ZONE '{$timezone}'");
    }
}