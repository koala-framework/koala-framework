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
 * Class for connecting to MySQL databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_Sqlite extends Zend_Db_Adapter_Pdo_Abstract
{

    /**
     * PDO type
     *
     * @var string
     */
     protected $_pdoType = 'sqlite';

    /**
     * Constructor.
     *
     * $config is an array of key/value pairs containing configuration
     * options.  Note that the SQLite options are different than most of
     * the other PDO adapters in that no username or password are needed.
     * Also, an extra config key "sqlite2" specifies compatibility mode.
     *
     * dbname    => (string) The name of the database to user (required,
     *                       use :memory: for memory-based database)
     *
     * sqlite2   => (boolean) PDO_SQLITE defaults to SQLite 3.  For compatibility
     *                        with an older SQLite 2 database, set this to TRUE.
     *
     * @param array $config An array of configuration keys.
     */
    public function __construct($config)
    {
        if (isset($config['sqlite2']) && $config['sqlite2']) {
            $this->_pdoType = 'sqlite2';
        }

        // SQLite uses no username/password.  Stub to satisfy parent::_connect()
        $this->_config['username'] = null;
        $this->_config['password'] = null;

        return parent::__construct($config);
    }

    /**
     * DSN builder
     */
    protected function _dsn()
    {
        return $this->_pdoType .':'. $this->_config['dbname'];
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' "
             . "UNION ALL SELECT name FROM sqlite_temp_master "
             . "WHERE type='table' ORDER BY name";

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
        $sql = "PRAGMA table_info($tableName)";
        $stmt = $this->query($sql);
        $result = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        $desc = array();
        foreach ($result as $key => $row) {
            $desc[$row['name']] = array(
                'SCHEMA_NAME' => null,
                'TABLE_NAME'  => $tableName,
                'COLUMN_NAME' => $row['name'],
                'COLUMN_POSITION' => null, // @todo
                'DATA_TYPE'   => $row['type'],
                'DEFAULT'     => $row['dflt_value'],
                'NULLABLE'    => ! (bool) $row['notnull'],
                'LENGTH'      => null,
                'SCALE'       => null,
                'PRECISION'   => null,
                'UNSIGNED'    => null, // @todo
                'PRIMARY'     => (bool) $row['pk'],
            );
        }
        return $desc;
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
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

        $sql .= " LIMIT $count";
        if ($offset > 0) {
            $sql .= " OFFSET $offset";
        }

        return $sql;
    }

}
