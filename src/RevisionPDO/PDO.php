<?php
namespace RevisionPDO;

use PHPSQLParser\PHPSQLParser;
use ReflectionObject;

/**
 * @author Alwin Garside <alwin@garsi.de>
 */
class PDO extends \PDO
{
    use WrapperTrait;

    /**
     * @param \PDO|string $dsnOrPDO
     * @param string      $username [optional]
     * @param string      $passwd   [optional]
     * @param array       $options  [optional]
     */
    public function __construct($dsnOrPDO, $username = null, $passwd = null, array $options = null)
    {
        if ($dsnOrPDO instanceof \PDO) {
            $this->wrappee = $dsnOrPDO;
        } else {
            $this->wrappee = new \PDO($dsnOrPDO, $username, $passwd, $options);
        }
    }

    /**
     * @param string $statement
     *
     * @return int
     */
    public function exec($statement)
    {
        $parser = new PHPSQLParser();
        $parsed = $parser->parse($statement);

        return $this->wrappee->exec($statement);
    }

    /**
     * @param string $statement
     * @param array  $driver_options
     *
     * @return bool|\PDOStatement
     */
    public function prepare(
        $statement, /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
        $driver_options = []
    ) {

        return $this->wrappee->prepare($statement, $driver_options);
    }
}
