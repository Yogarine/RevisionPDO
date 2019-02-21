<?php
namespace RevisionPDO\Adapter;

use PDO;
use PHPSQLParser\PHPSQLParser;

class DefaultAdapter
{
    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var \PHPSQLParser\PHPSQLParser
     */
    protected $sqlParser;

    /**
     * @param \PDO                       $pdo
     * @param \PHPSQLParser\PHPSQLParser $sqlParser
     */
    public function __construct(PDO $pdo, PHPSQLParser $sqlParser)
    {
        $this->pdo       = $pdo;
        $this->sqlParser = $sqlParser;
    }

    /**
     * @param  string  $timezone
     */
    public function setTimeZone($timezone)
    {
        trigger_error(
            "RevisionPDO is unable to set timezone to {$timezone} for PDO driver " .
                "'{$this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)}'",
            E_USER_WARNING
        );
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @link http://php.net/manual/en/pdo.prepare.php
     *
     * @param  string  $statement       This must be a valid SQL statement for the target database server.
     * @param  array   $driver_options  [optional] This array holds one or more key=&gt;value pairs to set attribute
     *                                  values for the PDOStatement object that this method returns. You would most
     *                                  commonly use this to set the PDO::ATTR_CURSOR value to PDO::CURSOR_SCROLL to
     *                                  request a scrollable cursor.
     *                                  Some drivers have driver specific options that may be set at prepare-time.
     *
     * @return \PDOStatement|bool       If the database server successfully prepares the statement, PDO::prepare returns
     *                                  a PDOStatement object.
     *                                  If the database server cannot successfully prepare the statement, PDO::prepare
     *                                  returns FALSE or emits PDOException (depending on error handling).
     *                                  Emulated prepared statements does not communicate with the database server so
     *                                  PDO::prepare</b> does not check the statement.
     */
    public function prepare(
        $statement, /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
        $driver_options = array()
    ) {
        return call_user_func_array(array($this->pdo, 'prepare'), func_get_args());
    }

    /**
     * Initiates a transaction.
     *
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO object
     * instance are not committed until you end the transaction by calling {@link PDO::commit()}. Calling
     * {@link PDO::rollBack()} will roll back all changes to the database and return the connection to autocommit mode.
     *
     * Some databases, including MySQL, automatically issue an implicit COMMIT when a database definition language (DDL)
     * statement such as DROP TABLE or CREATE TABLE is issued within a transaction. The implicit COMMIT will prevent you
     * from rolling back any other changes within the transaction boundary.
     *
     * @link http://php.net/manual/en/pdo.begintransaction.php
     *
     * @return bool           TRUE on success or FALSE on failure.
     * @throws \PDOException  If there is already a transaction started or the driver does not support transactions
     *                        Note: An exception is raised even when the PDO::ATTR_ERRMODE attribute is not
     *                        PDO::ERRMODE_EXCEPTION.
     */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commits a transaction.
     *
     * @link http://php.net/manual/en/pdo.commit.php
     *
     * @return bool  TRUE on success or FALSE on failure.
     */
    public function commit()
    {
        return $this->pdo->commit();
    }

    /**
     * Rolls back a transaction.
     *
     * @link http://php.net/manual/en/pdo.rollback.php
     *
     * @return bool  TRUE on success or FALSE on failure.
     */
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }

    /**
     * Checks if inside a transaction.
     *
     * @link http://php.net/manual/en/pdo.intransaction.php
     *
     * @return bool  TRUE if a transaction is currently active, and FALSE if not.
     */
    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    /**
     * Set an attribute.
     *
     * @link http://php.net/manual/en/pdo.setattribute.php
     *
     * @param  int    $attribute
     * @param  mixed  $value
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function setAttribute($attribute, $value)
    {
        return $this->pdo->setAttribute($attribute, $value);
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     *
     * @link http://php.net/manual/en/pdo.exec.php
     *
     * @param  string  $statement  The SQL statement to prepare and execute.
     *                             Data inside the query should be properly escaped.
     *
     * @return int     PDO::exec() returns the number of rows that were modified or deleted by the SQL
     *                 statement you issued. If no rows were affected, PDO::exec returns 0.
     *                 This function may return Boolean FALSE, but may also return a non-Boolean value which
     *                 evaluates to FALSE</b>. Please read the section on Booleans for more information.
     *                 Use the === operator for testing the return value of this function.
     *                 The following example incorrectly relies on the return value of PDO::exec(), wherein
     *                 a statement that affected 0 rows results in a call to die():
     *                 <code>
     *                     $db->exec() or die(print_r($db->errorInfo(), true));
     *                 </code>
     */
    public function exec($statement)
    {
        $metadata = $this->sqlParser->parse($statement, true);


        return $this->pdo->exec($statement);
    }

    /**
     * Executes an SQL statement, returning a result set as a PDOStatement object.
     *
     * @link http://php.net/manual/en/pdo.query.php
     *
     * @param  string  $statement  The SQL statement to prepare and execute.
     *                             Data inside the query should be properly escaped.
     * @param  int     $mode       The fetch mode must be one of the PDO::FETCH_* constants.
     * @param  mixed   $arg3       The second and following parameters are the same as the parameters for
     *                             PDOStatement::setFetchMode.
     * @param  array $ctorargs     Arguments of custom class constructor when the `$mode` parameter is set to
     *                             PDO::FETCH_CLASS.
     *
     * @return \PDOStatement|bool  PDO::query() returns a PDOStatement object, or FALSE on failure.
     *
     * @see PDOStatement::setFetchMode For a full description of the second and following parameters.
     */
    public function query(
        $statement,
        $mode = PDO::ATTR_DEFAULT_FETCH_MODE,
        $arg3 = null, /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
        $ctorargs = array()
    ) {
        return call_user_func_array(array($this->pdo, 'query'), func_get_args());
    }

    /**
     * Returns the ID of the last inserted row or sequence value.
     *
     * @link http://php.net/manual/en/pdo.lastinsertid.php
     *
     * @param  string  $name  Name of the sequence object from which the ID should be returned.
     *
     * @return string  If a sequence name was not specified for the $name parameter, PDO::lastInsertId() returns
     *                 a string representing the row ID of the last row that was inserted into the database.
     *                 If a sequence name was specified for the $name parameter, PDO::lastInsertId() returns a
     *                 string representing the last value retrieved from the specified sequence object.
     *                 If the PDO driver does not support this capability, PDO::lastInsertId() triggers an
     *                 IM001 SQLSTATE.
     */
    public function lastInsertId($name = null)
    {
        return call_user_func_array(array($this->pdo, 'lastInsertId'), func_get_args());
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the database handle.
     *
     * @link http://php.net/manual/en/pdo.errorcode.php
     *
     * @return mixed  An SQLSTATE, a five characters alphanumeric identifier defined in the ANSI SQL-92 standard.
     *                Briefly, an SQLSTATE consists of a two characters class value followed by a three characters
     *                subclass value. A class value of 01 indicates a warning and is accompanied by a return code of
     *                SQL_SUCCESS_WITH_INFO. Class values other than '01', except for the class 'IM', indicate an error.
     *                The class 'IM' is specific to warnings and errors that derive from the implementation of PDO
     *                (or perhaps ODBC, if you're using the ODBC driver) itself. The subclass value '000' in any class
     *                indicates that there is no subclass for that SQLSTATE.
     *
     *                PDO::errorCode() only retrieves error codes for operations performed directly on the database
     *                handle. If you create a PDOStatement object through PDO::prepare() or PDO::query() and invoke an
     *                error on the statement handle, PDO::errorCode() will not reflect that error. You must call
     *                PDOStatement::errorCode() to return the error code for an operation performed on a particular
     *                statement handle.
     *
     *                Returns NULL if no operation has been run on the database handle.
     */
    public function errorCode()
    {
        return $this->pdo->errorCode();
    }

    /**
     * Fetch extended error information associated with the last operation on the database handle.
     *
     * @link http://php.net/manual/en/pdo.errorinfo.php
     *
     * @return string[]  PDO::errorInfo() returns an array of error information about the last operation performed by
     *                   this database handle. The array consists of the following fields:
     *                    0: SQLSTATE error code (a five characters alphanumeric identifier defined in the ANSI
     *                       SQL standard).
     *                    1: Driver-specific error code.
     *                    2: Driver-specific error message.
     *
     *                   If the SQLSTATE error code is not set or there is no driver-specific error, the elements
     *                   following element 0 will be set to NULL.
     *                   PDO::errorInfo() only retrieves error information for operations performed directly on the
     *                   database handle. If you create a PDOStatement object through PDO::prepare() or PDO::query() and
     *                   invoke an error on the statement handle, PDO::errorInfo() will not reflect the error from the
     *                   statement handle. You must call PDOStatement::errorInfo() to return the error information for
     *                   an operation performed on a particular statement handle.
     */
    public function errorInfo()
    {
        return $this->pdo->errorInfo();
    }

    /**
     * Retrieve a database connection attribute.
     *
     * @link http://php.net/manual/en/pdo.getattribute.php
     *
     * @param  int  $attribute  One of the PDO::ATTR_* constants. The constants that apply to database connections are
     *                          as follows:
     *                            - PDO::ATTR_AUTOCOMMIT
     *                            - PDO::ATTR_CASE
     *                            - PDO::ATTR_CLIENT_VERSION
     *                            - PDO::ATTR_CONNECTION_STATUS
     *                            - PDO::ATTR_DRIVER_NAME
     *                            - PDO::ATTR_ERRMODE
     *                            - PDO::ATTR_ORACLE_NULLS
     *                            - PDO::ATTR_PERSISTENT
     *                            - PDO::ATTR_PREFETCH
     *                            - PDO::ATTR_SERVER_INFO
     *                            - PDO::ATTR_SERVER_VERSION
     *                            - PDO::ATTR_TIMEOUT
     *
     * @return mixed            A successful call returns the value of the requested PDO attribute.
     *                          An unsuccessful call returns null.
     */
    public function getAttribute($attribute)
    {
        return $this->pdo->getAttribute($attribute);
    }

    /**
     * Quotes a string for use in a query.
     *
     * @link http://php.net/manual/en/pdo.quote.php
     *
     * @param  string  $string          The string to be quoted.
     * @param  int     $parameter_type  Provides a data type hint for drivers that have alternate quoting styles.
     *
     * @return string  A quoted string that is theoretically safe to pass into an SQL statement.
     *                 Returns FALSE if the driver does not support quoting in this way.
     */
    public function quote($string, $parameter_type = PDO::PARAM_STR)
    {
        return call_user_func_array(array($this->pdo, 'quote'), func_get_args());
    }
}
