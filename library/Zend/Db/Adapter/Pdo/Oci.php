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

/**
 * Zend_Db_Adapter_Pdo
 */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';

/**
 * Zend_Db_Adapter_Exception
 */
require_once 'Zend/Db/Adapter/Exception.php';

/**
 * Class for connecting to Oracle databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_Oci extends Zend_Db_Adapter_Pdo_Abstract
{

    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'oci';

    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        // baseline of DSN parts
        $dsn = $this->_config;

        $tns = 'dbname=//' . $dsn['host'];
        if (isset($dsn['port'])) {
            $tns .= ':' . $dsn['port'];
        }
        $tns .= '/' . $dsn['dbname'];

        return $this->_pdoType . ':' . $tns;
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
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
            $desc[$row['column_name']] = array(
                'SCHEMA_NAME' => null,
                'TABLE_NAME'  => $row['table_name'],
                'COLUMN_NAME' => $row['column_name'],
                'COLUMN_POSITION' => null,
                'DATA_TYPE'   => $row['data_type'],
                'DEFAULT'     => $row['data_default'],
                'NULLABLE'    => (bool) ($row['nullable'] == 'Y'),
                'LENGTH'      => $row['data_length'],
                'SCALE'       => $row['data_scale'],
                'PRECISION'   => $row['data_precision'],
                'UNSIGNED'    => null,
                'PRIMARY'     => (bool) 0
            );
        }
        return $desc;
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
     * Gets the last inserted ID.
     *
     * @param string $sequenceName   Name of table (or sequence) associated with sequence.
     * @return integer
     * @throws Zend_Db_Adapter_Exception
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        if (!$tableName) {
            throw new Zend_Db_Adapter_Exception("Sequence name must be specified");
        }
        $this->_connect();
        $sequenceName = $tableName . '_seq';
        $data = $this->fetchCol("SELECT $sequenceName.currval FROM dual");
        return $data[0]; //we can't fail here, right? if the sequence doesn't exist we should fail earlier.
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param string $sql
     * @param integer $count
     * @param integer $offset
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
        $count = intval($count);
        if ($count <= 0) {
            throw new Zend_Db_Adapter_Exception("LIMIT argument count=$count is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            throw new Zend_Db_Adapter_Exception("LIMIT argument offset=$offset is not valid");
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

}
