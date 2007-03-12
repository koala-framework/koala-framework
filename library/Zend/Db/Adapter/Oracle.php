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
     * database => Either the name of the local Oracle instance, or the
     *             name of the entry in tnsnames.ora to which you want to connect.
     *
     * @todo fix inconsistency between "database" used here and "dbname" use elsewhere
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
     * database => Either the name of the local Oracle instance, or the
     *             name of the entry in tnsnames.ora to which you want to connect.
     *
     * @param array $config An array of configuration keys.
     * @throws Zend_Db_Adapter_Exception
     */
    public function __construct($config)
    {
        // make sure the config array exists
        if (! is_array($config)) {
            throw new Zend_Db_Adapter_Exception('must pass a config array');
        }

        // we need at least a dbname
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
        /**
         * @todo should check resource here
         */
        if ($this->_connection) {
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
     * Gets the last inserted ID.
     *
     * @param string $sequenceName   Name of sequence to query.
     * @return integer
     * @throws Zend_Db_Adapter_Oracle_Exception
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        if (!$tableName) {
            throw new Zend_Db_Adapter_Exception("Sequence name must be specified");
        }
        $this->_connect();
        $data = $this->fetchCol("SELECT $tableName.currval FROM dual");
        return $data[0]; //we can't fail here, right? if the sequence doesn't exist we should fail earlier.
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
     * SCHEMA_NAME => string; name of database or schema
     * TABLE_NAME  => string;
     * COLUMN_NAME => string; column name
     * COLUMN_POSITION => number; ordinal position of column in table
     * DATA_TYPE   => string; SQL datatype name of column
     * DEFAULT     => string; default expression of column, null if none
     * NULLABLE    => boolean; true if column can have nulls
     * LENGTH      => number; length of CHAR/VARCHAR
     * SCALE       => number; scale of NUMERIC/DECIMAL
     * PRECISION   => number; precision of NUMERIC/DECIMAL
     * UNSIGNED    => boolean; unsigned property of an integer type
     * PRIMARY     => boolean; true if column is part of the primary key
     *
     * @todo Discover column position.
     * @todo Discover integer unsigned property.
     * @todo Improve discovery of primary key columns.
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $tableName = strtoupper($tableName);
        $sql = "SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, DATA_DEFAULT, NULLABLE, DATA_LENGTH, DATA_SCALE, DATA_PRECISION
            FROM ALL_TAB_COLUMNS
            WHERE TABLE_NAME = '$tableName'";
        $stmt = $this->query($sql);
        $result = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        $desc = array();
        foreach ($result as $key => $row) {
            $desc[$row['COLUMN_NAME']] = array(
                'SCHEMA_NAME' => '',
                'TABLE_NAME'  => $row['TABLE_NAME'],
                'COLUMN_NAME' => $row['COLUMN_NAME'],
                'COLUMN_POSITION' => null, // @todo
                'DATA_TYPE'   => $row['DATA_TYPE'],
                'DEFAULT'     => $row['DATA_DEFAULT'],
                'NULLABLE'    => (bool) ($row['NULLABLE'] == 'Y'),
                'LENGTH'      => $row['DATA_LENGTH'],
                'SCALE'       => $row['DATA_SCALE'],
                'PRECISION'   => $row['DATA_PRECISION'],
                'UNSIGNED'    => null, // @todo
                'PRIMARY'     => (bool) 0
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
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    public function _quote($value)
    {
        $value = str_replace("'", "''", $value);
        return "'" . $value . "'";
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

