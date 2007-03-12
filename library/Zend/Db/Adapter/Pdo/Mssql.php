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
 * Zend_Db_Adapter_Pdo_Abstract
 */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';

/**
 * Zend_Db_Adapter_Exception
 */
require_once 'Zend/Db/Adapter/Exception.php';

/**
 * Class for connecting to Microsoft SQL Server databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_Mssql extends Zend_Db_Adapter_Pdo_Abstract
{
    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'mssql';

    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        // baseline of DSN parts
        $dsn = $this->_config;

        // don't pass the username and password in the DSN
        unset($dsn['username']);
        unset($dsn['password']);
        if (isset($dsn['port'])) {
            $dsn['host'] .= ',' . $port;
            unset($dsn['port']);
        }

        // use all remaining parts in the DSN
        foreach ($dsn as $key => $val) {
            $dsn[$key] = "$key=$val";
        }

        // $dsn = $this->_pdoType . ':' . implode(';', $dsn);
        $dsn = 'mssql' . ':' . implode(';', $dsn);
        return $dsn;
    }

    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        $q = $this->getQuoteIdentifierSymbol();
        $ident = str_replace("$q[1]", "$q[1]$q[1]", $ident);
        return $q[0] . $ident . $q[1];
    }

    /**
     * @return array
     */
    public function getQuoteIdentifierSymbol()
    {
        return array('[', ']');
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $sql = "SELECT name FROM sysobjects WHERE type = 'U' ORDER BY name";
        return $this->fetchCol($sql);
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
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $sql = "exec sp_columns @table_name = " . $this->quoteIdentifier($tableName);
        $stmt = $this->query($sql);
        $result = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);

        $sql = "exec sp_pkeys @table_name = " . $this->quoteIdentifier($tableName);
        $stmt = $this->query($sql);
        $primaryKeysResult = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        foreach ($primaryKeysResult as $row) {
            $primaryKeyColumn[$row['column_name']] = true;
        }

        $desc = array();
        foreach ($result as $key => $row) {
            list($type, $rest) = explode(' ', $row['type_name'], 2);

            if (array_key_exists($primaryKeyColumn, $row['column_name'])) {
                $row['is_primary'] = true;
            }

            $desc[$row['column_name']] = array(
                'SCHEMA_NAME' => null,
                'TABLE_NAME'  => $row['table_name'],
                'COLUMN_NAME' => $row['column_name'],
                'COLUMN_POSITION' => null, // @todo
                'DATA_TYPE'   => $type,
                'DEFAULT'     => $row['column_def'],
                'NULLABLE'    => (bool) $row['nullable'],
                'LENGTH'      => $row['length'],
                'SCALE'       => $row['scale'],
                'PRECISION'   => $row['precision'],
                'UNSIGNED'    => null, // @todo
                'PRIMARY'     => (bool) $row['is_primary']
            );
        }
        return $desc;
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @link http://lists.bestpractical.com/pipermail/rt-devel/2005-June/007339.html
     *
     * @param string $sql
     * @param integer $count
     * @param integer $offset OPTIONAL
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

        $orderby = stristr($sql, 'ORDER BY');
        if ($orderby !== false) {
            $sort = (stripos($orderby, 'desc') !== false) ? 'desc' : 'asc';
            $order = str_ireplace('ORDER BY', '', $orderby);
            $order = trim(preg_replace('/ASC|DESC/i', '', $order));
        }

        $sql = preg_replace('/^SELECT\s/i', 'SELECT TOP ' . ($count+$offset) . ' ', $sql);

        $sql = 'SELECT * FROM (SELECT TOP ' . $count . ' * FROM (' . $sql . ') AS inner_tbl';
        if ($orderby !== false) {
            $sql .= ' ORDER BY ' . $order . ' ';
            $sql .= (stripos($sort, 'asc') !== false) ? 'DESC' : 'ASC';
        }
        $sql .= ') AS outer_tbl';
        if ($orderby !== false) {
            $sql .= ' ORDER BY ' . $order . ' ' . $sort;
        }

        return $sql;
    }

    /**
     * Gets the last inserted ID.
     *
     * @param  string $sequenceName Not used in this adapter.
     * @return integer
     * @throws Zend_Db_Adapter_Exception
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        $sql = 'SELECT SCOPE_IDENTITY()';
        return (int)$this->fetchOne($sql);
    }

}
