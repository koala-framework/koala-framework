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
 *
 */

/**
 * Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * Zend_Db_Adapter_Exception
 */
require_once 'Zend/Db/Adapter/Mysqli/Exception.php';

/**
 * Zend_Db_Profiler
 */
require_once 'Zend/Db/Profiler.php';

/**
 * Zend_Db_Select
 */
require_once 'Zend/Db/Select.php';

/**
 * Zend_Db_Statement_Mysqli
 */
require_once 'Zend/Db/Statement/Mysqli.php';

class Zend_Db_Adapter_Mysqli extends Zend_Db_Adapter_Abstract
{

    /**
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    protected function _quote($value)
    {
        $this->_connect();
        return "'" . $this->_connection->real_escape_string($value) . "'";
    }

    /**
     * Returns the symbol the adapter uses for delimiting identifiers.
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return "`";
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        return $this->fetchCol('SHOW TABLES');
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
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        // @todo: use INFORMATION_SCHEMA someday when
        // MySQL's implementation isn't dog slow.

        if ($schemaName) {
            $sql = 'DESCRIBE ' . $this->quoteIdentifier("$schemaName.$tableName");
        } else {
            $sql = 'DESCRIBE ' . $this->quoteIdentifier($tableName);
        }
        $stmt = $this->query($sql);
        $result = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        $desc = array();

        $row_defaults = array(
            'Length'    => null,
            'Scale'     => null,
            'Precision' => null,
            'Unsigned'  => null
        );
        $i = 1;
        $p = 1;
        foreach ($result as $key => $row) {
            $row = array_merge($row_defaults, $row);
            if (preg_match('/unsigned/', $row['Type'])) {
                $row['Unsigned'] = true;
            }
            if (preg_match('/^((?:var)?char)\((\d+)\)/', $row['Type'], $matches)) {
                $row['Type'] = $matches[1];
                $row['Length'] = $matches[2];
            } else if (preg_match('/^decimal\((\d+),(\d+)\)/', $row['Type'], $matches)) {
                $row['Type'] = 'decimal';
                $row['Precision'] = $matches[1];
                $row['Scale'] = $matches[2];
            } else if (preg_match('/^((?:big|medium|small)?int)\((\d+)\)/', $row['Type'], $matches)) {
                $row['Type'] = $matches[1];
                // The optional argument of a MySQL int type is not precision
                // or length; it is only a hint for display width.
            }
            $desc[$row['Field']] = array(
                'SCHEMA_NAME'      => null,
                'TABLE_NAME'       => $tableName,
                'COLUMN_NAME'      => $row['Field'],
                'COLUMN_POSITION'  => $i,
                'DATA_TYPE'        => $row['Type'],
                'DEFAULT'          => $row['Default'],
                'NULLABLE'         => (bool) ($row['Null'] == 'YES'),
                'LENGTH'           => $row['Length'],
                'PRECISION'        => $row['Precision'],
                'SCALE'            => $row['Scale'],
                'UNSIGNED'         => $row['Unsigned'],
                'PRIMARY'          => (bool) (strtoupper($row['Key']) == 'PRI'),
                'PRIMARY_POSITION' => ((bool) (strtoupper($row['Key']) == 'PRI') ? $p++ : 0)
            );
            ++$i;
        }
        return $desc;
    }

    /**
     * Creates a connection to the database.
     *
     * @return void
     * @throws Zend_Db_Adapter_Mysqli_Exception
     */
    protected function _connect()
    {
        if ($this->_connection) {
            return;
        }
        // Suppress connection warnings here.
        // Throw an exception instead.
        @$this->_connection = new mysqli(
            $this->_config['host'],
            $this->_config['username'],
            $this->_config['password'],
            $this->_config['dbname']
        );
        if ($this->_connection === false || mysqli_connect_errno()) {
            throw new Zend_Db_Adapter_Mysqli_Exception(mysqli_connect_error());
        }
    }

    /**
     * Prepare a statement and return a PDOStatement-like object.
     *
     * @param  string  $sql  SQL query
     * @return Zend_Db_Statement_Mysqli
     */
    public function prepare($sql)
    {
        $this->_connect();
        $stmt = new Zend_Db_Statement_Mysqli($this, $sql);
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }

    /**
     * Gets the last inserted ID.
     *
     * @param  string $tableName   OPTIONAL
     * @param  string $primaryKey  OPTIONAL
     * @return int
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        $mysqli = $this->_connection;
        return $mysqli->insert_id;
    }

    /**
     * Begin a transaction.
     *
     * @return void
     */
    protected function _beginTransaction()
    {
        $this->_connect();
        $this->_connection->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @return void
     */
    protected function _commit()
    {
        $this->_connect();
        $this->_connection->commit();
    }

    /**
     * Roll-back a transaction.

     * @return void
     */
    protected function _rollBack()
    {
        $this->_connect();
        $this->_connection->rollBack();
    }

    /**
     * Set the fetch mode.
     *
     * @param int $mode
     * @return void
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case Zend_Db::FETCH_LAZY:
            case Zend_Db::FETCH_ASSOC:
            case Zend_Db::FETCH_NUM:
            case Zend_Db::FETCH_BOTH:
            case Zend_Db::FETCH_NAMED:
            case Zend_Db::FETCH_OBJ:
                $this->_fetchMode = $mode;
                break;
            default:
                throw new Zend_Db_Adapter_Mysqli_Exception('Invalid fetch mode specified');
                break;
        }
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param string $sql
     * @param int $count
     * @param int $offset OPTIONAL
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
        $count = intval($count);
        if ($count <= 0) {
            throw new Zend_Db_Adapter_Mysqli_Exception("LIMIT argument count=$count is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            throw new Zend_Db_Adapter_Mysqli_Exception("LIMIT argument offset=$offset is not valid");
        }

        $sql .= " LIMIT $count";
        if ($offset > 0) {
            $sql .= " OFFSET $offset";
        }

        return $sql;
    }

}
