<?php
/*  RevisionPDO
    Copyright © 2018-2019 Alwin Garside
    All rights reserved.

    Redistribution and use in source and binary forms, with or without
    modification, are permitted provided that the following conditions are met:

     1. Redistributions of source code must retain the above copyright
        notice, this list of conditions and the following disclaimer.

     2. Redistributions in binary form must reproduce the above copyright
        notice, this list of conditions and the following disclaimer in the
        documentation and/or other materials provided with the distribution.

    THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
    IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
    THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
    PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR
    CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
    EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
    PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;
    OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
    WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR
    OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
    ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

    The views and conclusions contained in the software and documentation are
    those of the authors and should not be interpreted as representing official
    policies, either expressed or implied, of the copyright holders. */

namespace RevisionPDO;

use RevisionPDO\Adapter\AdapterFactory;
use RevisionPDO\Adapter\DefaultAdapter;

/**
 * @author Alwin Garside <alwin@garsi.de>
 * @license https://opensource.org/licenses/BSD-2-Clause 2-Clause BSD License
 */
class PDO extends \PDO
{
    const ATTR_INIT_COMMAND        = '__REVISION_PDO_ATTR_INIT_COMMAND';
    const ATTR_TIME_ZONE           = '__REVISION_PDO_ATTR_TIME_ZONE';
    const ATTR_METADATA_ENABLED    = '__REVISION_PDO_METADATA_ENABLED';
    const ATTR_METADATA_TABLE      = '__REVISION_PDO_METADATA_TABLE';
    const ATTR_OPERATOR_NAME       = '__REVISION_PDO_ATTR_OPERATOR_NAME';
    const ATTR_OPERATOR_IP_ADDRESS = '__REVISION_PDO_ATTR_OPERATOR_IP_ADDRESS';

    /**
     * @var \RevisionPDO\Adapter\DefaultAdapter
     */
    private $adapter;

    /**
     * The parent is not called on purpose. We're wrapping the actual PDO, and only extending the PDO class to be
     * compatible.
     *
     * @noinspection PhpMissingParentConstructorInspection
     *
     * @param  string | \PDO | \RevisionPDO\Adapter\DefaultAdapter  $dsn
     * @param  string                                               $username
     * @param  string                                               $passwd
     * @param  array                                                $options
     */
    public function __construct(
        $dsn,
        $username = null,
        $passwd = null,
        array $options = null
    ) {
        if (! $dsn instanceof DefaultAdapter) {
            if (! $dsn instanceof \PDO) {
                $dsn = new \PDO($dsn, $username, $passwd, $options);
            }

            $operator = new Operator(
                isset($options[self::ATTR_TIME_ZONE]) ? $options[self::ATTR_TIME_ZONE] : null,
                isset($options[self::ATTR_OPERATOR_IP_ADDRESS]) ? $options[self::ATTR_OPERATOR_IP_ADDRESS] : null
            );

            $adapterFactory = new AdapterFactory;
            $dsn = $adapterFactory->make($dsn, $operator);
        }
        $this->adapter = $dsn;

        if (isset($options[self::ATTR_METADATA_ENABLED]) && $options[self::ATTR_METADATA_ENABLED]) {
            $this->adapter->enableMetadata();
        }

        if (isset($options[self::ATTR_TIME_ZONE])) {
            $this->adapter->setTimeZone($options[self::ATTR_TIME_ZONE]);
        }

        if (isset($options[self::ATTR_INIT_COMMAND])) {
            $this->adapter->exec($options[self::ATTR_INIT_COMMAND]);
        }
    }

    /**
     * Prepares a statement for execution and returns a statement object.
     *
     * @link http://php.net/manual/en/pdo.prepare.php
     *
     * @param  string  $statement       This must be a valid SQL statement for the target database server.
     * @param  array   $driver_options  This array holds one or more key => value pairs to set attribute values for the
     *                                  PDOStatement object that this method returns. You would most commonly use this
     *                                  to set the PDO::ATTR_CURSOR value to PDO::CURSOR_SCROLL to request a scrollable
     *                                  cursor.
     *                                  Some drivers have driver specific options that may be set at prepare-time.
     *
     * @return \PDOStatement|bool       If the database server successfully prepares the statement, PDO::prepare()
     *                                  returns a PDOStatement object.
     *                                  If the database server cannot successfully prepare the statement, PDO::prepare()
     *                                  returns FALSE or emits PDOException (depending on error handling).
     *                                  Emulated prepared statements does not communicate with the database server so
     *                                  PDO::prepare() does not check the statement.
     */
    public function prepare(
        $statement, /** @noinspection PhpSignatureMismatchDuringInheritanceInspection */
        $driver_options = array()
    ) {
        return call_user_func_array(array($this->adapter, 'prepare'), func_get_args());
    }

    /**
     * Initiates a transaction.
     *
     * Turns off autocommit mode. While autocommit mode is turned off, changes made to the database via the PDO object
     * instance are not committed until you end the transaction by calling PDO::commit(). Calling PDO::rollBack() will
     * roll back all changes to the database and return the connection to autocommit mode.
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
        return $this->adapter->beginTransaction();
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
        return $this->adapter->commit();
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
        return $this->adapter->rollBack();
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
        return $this->adapter->inTransaction();
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
        return $this->adapter->setAttribute($attribute, $value);
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     *
     * @link http://php.net/manual/en/pdo.exec.php
     *
     * @param  string  $statement  The SQL statement to prepare and execute.
     *                             Data inside the query should be properly escaped.
     *
     * @return int     PDO::exec() returns the number of rows that were modified or deleted by the SQL statement you
     *                 issued. If no rows were affected, PDO::exec returns 0.
     *                 This function may return Boolean FALSE, but may also return a non-Boolean value which evaluates
     *                 to FALSE. Please read the section on Booleans for more information. Use the === operator for
     *                 testing the return value of this function.
     *                 The following example incorrectly relies on the return value of PDO::exec(), wherein a statement
     *                 that affected 0 rows results in a call to die():
     *                 <code>
     *                     $db->exec() or die(print_r($db->errorInfo(), true));
     *                 </code>
     */
    public function exec($statement)
    {
        return $this->adapter->exec($statement);
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
        return call_user_func_array(array($this->adapter, 'query'), func_get_args());
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
        return call_user_func_array(array($this->adapter, 'lastInsertId'), func_get_args());
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
        return $this->adapter->errorCode();
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
        return $this->adapter->errorInfo();
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
        return $this->adapter->getAttribute($attribute);
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
        return call_user_func_array(array($this->adapter, 'quote'), func_get_args());
    }
}
