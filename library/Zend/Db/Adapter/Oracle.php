<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Db_Adapter_Abstract */
require_once 'Zend/Db/Adapter/Abstract.php';

/** Zend_Db_Adapter_Oracle_Exception */
require_once 'Zend/Db/Adapter/Oracle/Exception.php';

/** Zend_Db_Statement_Oracle */
require_once 'Zend/Db/Statement/Oracle.php';

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Oracle extends Zend_Db_Adapter_Abstract
{
    /**
     * User-provided configuration.
     *
     * Basic keys are:
     *
     * username => (string) Connect to the database as this username.
     * password => (string) Password associated with the username.
     * dbname   => Either the name of the local Oracle instance, or the
     *             name of the entry in tnsnames.ora to which you want to connect.
     *
     * @var array
     */
    protected $_config = array(
        'dbname'       => null,
        'username'     => null,
        'password'     => null,
    );

    /**
     * @var integer
     */
    protected $_execute_mode = OCI_COMMIT_ON_SUCCESS;

    /**
     * Constructor.
     *
     * $config is an array of key/value pairs containing configuration
     * options.  These options are common to most adapters:
     *
     * username => (string) Connect to the database as this username.
     * password => (string) Password associated with the username.
     * dbname   => Either the name of the local Oracle instance, or the
     *             name of the entry in tnsnames.ora to which you want to connect.
     *
     * @param array $config An array of configuration keys.
     * @throws Zend_Db_Adapter_Exception
     */
    public function __construct(array $config)
    {
        if (! array_key_exists('password', $config) || ! array_key_exists('username', $config)) {
            throw new Zend_Db_Adapter_Exception('config array must have at least a username and a password');
        }

        // @todo Let this protect backward-compatibility for one release, then remove
        if (array_key_exists('database', $config) || ! array_key_exists('dbname', $config)) {
            $config['dbname'] = $config['database'];
            unset($config['database']);
            trigger_error("Deprecated config key 'database', use 'dbname' instead.", E_USER_NOTICE);
        }

        // keep the config
        $this->_config = array_merge($this->_config, (array) $config);

        // create a profiler object
        $enabled = false;
        if (array_key_exists('profiler', $this->_config)) {
            $enabled = (bool) $this->_config['profiler'];
            unset($this->_config['profiler']);
        }

        $this->_profiler = new Zend_Db_Profiler($enabled);
    }

    /**
     * Creates a connection resource.
     *
     * @return void
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    protected function _connect()
    {
        if (is_resource($this->_connection)) {
            // connection already exists
            return;
        }

        if (!extension_loaded('oci8')) {
            throw new Zend_DB_Adapter_Oracle_Exception('The OCI8 extension is required for this adapter but not loaded');
        }

        if (isset($this->_config['dbname'])) {
            $this->_connection = oci_connect(
                $this->_config['username'],
                $this->_config['password'],
                $this->_config['dbname']);
        } else {
            $this->_connection = oci_connect(
                $this->_config['username'],
                $this->_config['password']);
        }

        // check the connection
        if (!$this->_connection) {
            throw new Zend_Db_Adapter_Oracle_Exception(oci_error());
        }
    }

    /**
     * Force the connection to close.
     *
     * @return void
     */
    public function closeConnection()
    {
        if (is_resource($this->_connection)) {
            oci_close($this->_connection);
        }
        $this->_connection = null;
    }

    /**
     * Returns an SQL statement for preparation.
     *
     * @param string $sql The SQL statement with placeholders.
     * @return Zend_Db_Statement_Oracle
     */
    public function prepare($sql)
    {
        $this->_connect();
        $stmt = new Zend_Db_Statement_Oracle($this, $sql);
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }

    /**
     * Quote a table identifier and alias.
     *
     * @param string|array|Zend_Db_Expr $ident The identifier or expression.
     * @param string $alias An alias for the table.
     * @return string The quoted identifier and alias.
     */
    public function quoteTableAs($ident, $alias)
    {
        // Oracle doesn't allow the 'AS' keyword between the table identifier/expression and alias.
        return $this->_quoteIdentifierAs($ident, $alias, ' ');
    }

    /**
     * Return the most recent value from the specified sequence in the database.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return integer
     */
    public function lastSequenceId($sequenceName)
    {
        $this->_connect();
        $value = $this->fetchOne('SELECT '.$this->quoteIdentifier($sequenceName).'.CURRVAL FROM dual');
        return $value;
    }

    /**
     * Generate a new value from the specified sequence in the database, and return it.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return integer
     */
    public function nextSequenceId($sequenceName)
    {
        $this->_connect();
        $value = $this->fetchOne('SELECT '.$this->quoteIdentifier($sequenceName).'.NEXTVAL FROM dual');
        return $value;
    }

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * Oracle does not support IDENTITY columns, so if the sequence is not
     * specified, this method returns null.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return integer
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        if ($tableName !== null) {
            $sequenceName = $tableName;
            if ($primaryKey) {
                $sequenceName .= "_$primaryKey";
            }
            $sequenceName .= '_seq';
            return $this->lastSequenceId($sequenceName);
        }

        // No support for IDENTITY columns; return null
        return null;
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $this->_connect();
        $data = $this->fetchCol('SELECT table_name FROM all_tables');
        return $data;
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     *
     * @todo Discover integer unsigned property.
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $sql = "SELECT TC.TABLE_NAME, TB.TABLESPACE_NAME, TC.COLUMN_NAME, TC.DATA_TYPE,
                TC.DATA_DEFAULT, TC.NULLABLE, TC.COLUMN_ID, TC.DATA_LENGTH,
                TC.DATA_SCALE, TC.DATA_PRECISION, C.CONSTRAINT_TYPE, CC.POSITION
            FROM ALL_TAB_COLUMNS TC
            LEFT JOIN (ALL_CONS_COLUMNS CC JOIN ALL_CONSTRAINTS C
                ON (CC.CONSTRAINT_NAME = C.CONSTRAINT_NAME AND CC.TABLE_NAME = C.TABLE_NAME AND C.CONSTRAINT_TYPE = 'P'))
              ON TC.TABLE_NAME = CC.TABLE_NAME AND TC.COLUMN_NAME = CC.COLUMN_NAME
            JOIN ALL_TABLES TB ON (TB.TABLE_NAME = TC.TABLE_NAME)
            WHERE TC.TABLE_NAME = ".$this->quote($tableName);
        if ($schemaName) {
            $sql .= " AND TB.TABLESPACE_NAME = ".$this->quote($schemaName);
        }
        $sql .= ' ORDER BY TC.COLUMN_ID';

        $stmt = $this->query($sql);

        // Use FETCH_NUM so we are not dependent on the CASE attribute of the PDO connection
        $result = $stmt->fetchAll(Zend_Db::FETCH_NUM);

        $table_name      = 0;
        $tablespace_name = 1;
        $column_name     = 2;
        $data_type       = 3;
        $data_default    = 4;
        $nullable        = 5;
        $column_id       = 6;
        $data_length     = 7;
        $data_scale      = 8;
        $data_precision  = 9;
        $constraint_type = 10;
        $position        = 11;

        $desc = array();
        foreach ($result as $key => $row) {
            $desc[$row[$column_name]] = array(
                'SCHEMA_NAME'      => $row[$tablespace_name],
                'TABLE_NAME'       => $row[$table_name],
                'COLUMN_NAME'      => $row[$column_name],
                'COLUMN_POSITION'  => $row[$column_id],
                'DATA_TYPE'        => $row[$data_type],
                'DEFAULT'          => $row[$data_default],
                'NULLABLE'         => (bool) ($row[$nullable] == 'Y'),
                'LENGTH'           => $row[$data_length],
                'SCALE'            => $row[$data_scale],
                'PRECISION'        => $row[$data_precision],
                'UNSIGNED'         => null, // @todo
                'PRIMARY'          => (bool) ($row[$constraint_type] == 'P'),
                'PRIMARY_POSITION' => $row[$position]
            );
        }
        return $desc;
    }

    /**
     * Leave autocommit mode and begin a transaction.
     *
     * @return void
     */
    protected function _beginTransaction()
    {
        $this->_setExecuteMode(OCI_DEFAULT);
    }

    /**
     * Commit a transaction and return to autocommit mode.
     *
     * @return void
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    protected function _commit()
    {
        if (!oci_commit($this->_connection)) {
            throw new Zend_Db_Adapter_Oracle_Exception(oci_error($this->_connection));
        }
        $this->_setExecuteMode(OCI_COMMIT_ON_SUCCESS);
    }

    /**
     * Roll back a transaction and return to autocommit mode.
     *
     * @return void
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    protected function _rollBack()
    {
        if (!oci_rollback($this->_connection)) {
            throw new Zend_Db_Adapter_Oracle_Exception(oci_error($this->_connection));
        }
        $this->_setExecuteMode(OCI_COMMIT_ON_SUCCESS);
    }

    /**
     * Set the fetch mode.
     *
     * @todo Support FETCH_CLASS and FETCH_INTO.
     *
     * @param integer $mode A fetch mode.
     * @return void
     * @throws Zend_Db_Adapter_Exception
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case Zend_Db::FETCH_NUM:   // seq array
            case Zend_Db::FETCH_ASSOC: // assoc array
            case Zend_Db::FETCH_BOTH:  // seq+assoc array
            case Zend_Db::FETCH_OBJ:   // object
                $this->_fetchMode = $mode;
                break;
            default:
                throw new Zend_Db_Adapter_Exception('Invalid fetch mode specified');
                break;
        }
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param string $sql
     * @param integer $count
     * @param integer $offset OPTIONAL
     * @return string
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    public function limit($sql, $count, $offset = 0)
    {
        $count = intval($count);
        if ($count <= 0) {
            throw new Zend_Db_Adapter_Oracle_Exception("LIMIT argument count=$count is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            throw new Zend_Db_Adapter_Oracle_Exception("LIMIT argument offset=$offset is not valid");
        }

        /**
         * Oracle does not implement the LIMIT clause as some RDBMS do.
         * We have to simulate it with subqueries and ROWNUM.
         * Unfortunately because we use the column wildcard "*", 
         * this puts an extra column into the query result set.
         */
        $limit_sql = "SELECT z2.*
            FROM (
                SELECT ROWNUM AS zend_db_rownum, z1.*
                FROM (
                    " . $sql . "
                ) z1
            ) z2
            WHERE z2.zend_db_rownum BETWEEN " . ($offset+1) . " AND " . ($offset+$count);
        return $limit_sql;
    }

    /**
     * @param integer $mode
     * @throws Zend_Db_Adapter_Exception
     */
    private function _setExecuteMode($mode)
    {
        switch($mode) {
            case OCI_COMMIT_ON_SUCCESS:
            case OCI_DEFAULT:
            case OCI_DESCRIBE_ONLY:
                $this->_execute_mode = $mode;
                break;
            default:
                throw new Zend_Db_Adapter_Exception('wrong execution mode specified');
                break;
        }
    }

    /**
     * @return
     */
    public function _getExecuteMode()
    {
        return $this->_execute_mode;
    }

}

