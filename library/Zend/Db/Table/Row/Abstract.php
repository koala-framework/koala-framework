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
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_Row_Abstract
{

    /**
     * The data for each column in the row (column_name => value).
     *
     * @var array
     */
    protected $_data = array();

    /**
     * This is set to a copy of $_data when the data is fetched from
     * a database, specified as a new tuple in the constructor, or
     * when dirty data is posted to the database with save().
     * @var array
     */ 
    protected $_cleanData = array(); 
 
    /**
     * Zend_Db_Table parent class or instance.
     *
     * @var Zend_Db_Table
     */
    protected $_table = null;

    /**
     * Connected is true if we have a reference to a live
     * Zend_Db_Table_Abstract object.
     * This is false after the Rowset has been deserialized.
     *
     * @var boolean
     */
    protected $_connected = true;

    /**
     * Name of the class of the Zend_Db_Table object.
     *
     * @var string
     */
    protected $_tableClass = null;

    /**
     * Primary row key(s).
     *
     * @var array
     */
    protected $_primary;

    /**
     * Constructor.
     *
     * Supported params for $config are:-
     * - table       = class name or object of type Zend_Db_Table_Abstract
     * - data        = values of columns in this row.
     *
     * @param  array $config OPTIONAL Array of user-specified config options.
     * @throws Zend_Db_Table_Row_Exception
     */
    public function __construct(array $config = array())
    {
        if (isset($config['table']) && $config['table'] instanceof Zend_Db_Table_Abstract) {
            $this->_table = $config['table'];
            $this->_tableClass = get_class($this->_table);
        }

        if (isset($config['data'])) {
            $this->_data = $config['data'];
            $this->_cleanData = $this->_data;
        }

        // Retrieve primary keys from table schema
        if ($table = $this->_getTable()) {
            $info = $this->_getTable()->info();
            $this->_primary = (array) $info['primary'];
        }
    }

    /**
     * Retrieve row field value
     *
     * @param  string $key The column key.
     * @return string      The mapped column value.
     * @throws Zend_Db_Table_Row_Exception
     */
    public function __get($key)
    {
        if (!array_key_exists($key, $this->_data)) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Specified column \"$key\" is not in the row");
        }

        return $this->_data[$key];
    }

    /**
     * Set row field value
     *
     * @param  string $key   The column key.
     * @param  mixed  $value The value for the property.
     * @return void
     * @throws Zend_Db_Table_Row_Exception
     */
    public function __set($key, $value)
    {
        // @todo this should permit a primary key value to be set
        // not all table have an auto-generated primary key
        if (in_array($key, $this->_primary)) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Changing the primary key value(s) is not allowed");
        }

        if (!array_key_exists($key, $this->_data)) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Specified column \"$key\" is not in the row");
        }

        $this->_data[$key] = $value;
    }

    /**
     * Test existence of row field
     *
     * @param  string  $key   The column key.
     * @return boolean
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->_data);
    }

    /**
     * Store table, primary key and data in serialized object
     *
     * @return array
     */
    public function __sleep()
    {
        return array('_tableClass', '_primary', '_data');
    }

    /**
     * Setup to do on wakeup.
     * A de-serialized Row should not be assumed to have access to a live
     * database connection, so set _connected = false.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->_connected = false;
        $this->_cleanData = $this->_data;
    }

    /**
     * Set the table object, to re-establish a live connection
     * to the database for a Row that has been de-serialized.
     *
     * @param Zend_Db_Table_Abstract $table
     * @return boolean
     * @throws Zend_Db_Table_Row_Exception
     */
    public function setTable(Zend_Db_Table_Abstract $table)
    {
        if ($table == null) {
            $this->_table = null;
            $this->_connected = false;
            return false;
        }

        $tableClass = get_class($table);
        if (! $table instanceof $this->_tableClass) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("The specified Table is of class $tableClass, expecting class to be instance of $this->_tableClass");
        }

        $this->_table = $table;
        $this->_tableClass = $tableClass;

        $info = $this->_table->info();

        if ($info['cols'] != array_keys($this->_data)) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception('The specified Table does not have the same columns as the Row');
        }

        if (! array_intersect((array) $this->_primary, $info['primary']) == (array) $this->_primary) {

            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("The specified Table '$tableClass' does not have the same primary key as the Row");
        }

        $this->_connected = true;
        return true;
    }

    /**
     * Query the class name of the Table object for which this
     * Row was created.
     *
     * @return string
     */
    public function getTableClass()
    {
        return $this->_tableClass;
    }

    /**
     * Saves the properties to the database.
     *
     * This performs an intelligent insert/update, and reloads the
     * properties with fresh data from the table on success.
     *
     * @return integer 0 on failure, 1 on success.
     * @throws Zend_Db_Table_Row_Exception
     */
    public function save()
    {

        // convenience var for the primary key name
        $keys = $this->_getPrimaryKey();
        $values = array_filter($keys);

        // check the primary key value for insert/update
        if (empty($values)) {

            // apply any built-in logic for row insertion
            $this->_insert();

            // attempt the insert.
            $result = $this->_getTable()->insert($this->_data);

            if (is_numeric($result)) {
                // insert worked, refresh with data from the table
                $this->_data[key($keys)] = $result;
                $this->_refresh();
            }
        } else {
            // has a primary key value, update only that key.
            $where = $this->_getWhereQuery(false);

            // apply any built-in logic for row update
            $this->_update();

            // execute cascading updates against dependent tables
            $depTables = $this->_getTable()->getDependentTables();
            if (!empty($depTables)) {
                $db = $this->_getTable()->getAdapter();
                $pkNew = $this->_getPrimaryKey(true);
                $pkOld = $this->_getPrimaryKey(false);
                $thisClass = get_class($this);
                foreach ($depTables as $tableClass) {
                    Zend_Loader::loadClass($tableClass);
                    $t = new $tableClass(array('db' => $db));
                    $t->_cascadeUpdate($this->getTableClass(), $pkOld, $pkNew);
                }
            }

            // return the result of the update attempt, no need to update the row object.
            $result = $this->_getTable()->update($this->_data, $where);

            if (is_int($result)) {
                // update worked, refresh with data from the table
                $this->_refresh();
            }
        }
        return $result;
    }

    /**
     * Deletes existing rows.
     *
     * @return int The number of rows deleted.
     */
    public function delete()
    {
        // has a primary key value, update only that key.
        $where = $this->_getWhereQuery();

        // apply any built-in logic for row deletion
        $this->_delete();

        // execute cascading deletes against dependent tables
        $depTables = $this->_getTable()->getDependentTables();
        if (!empty($depTables)) {
            $db = $this->_getTable()->getAdapter();
            $pk = $this->_getPrimaryKey();
            $thisClass = get_class($this);
            foreach ($depTables as $tableClass) {
                Zend_Loader::loadClass($tableClass);
                $t = new $tableClass(array('db' => $db));
                $t->_cascadeDelete($this->getTableClass(), $pk);
            }
        }

        $result = $this->_getTable()->delete($where);

        // reset all fields to null
        $this->_data = array_combine(array_keys($this->_data), array_fill(0, count($this->_data), null));

        return $result;
    }

    /**
     * Returns the column/value data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Sets all data in the row from an array.
     *
     * @param array $data
     */
    public function setFromArray($data)
    {
        foreach ($data as $key => $val) {
            if (array_key_exists($key, $this->_data)) {
                $this->_data[$key] = $val;
            }
        }
    }

    /**
     * Retrieves an instance of the parent table.
     *
     * @return Zend_Db_Table
     */
    protected function _getTable()
    {
        if (!$this->_connected) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception('Cannot save a Row unless it is connected');
        }
        return $this->_table;
    }

    /**
     * Retrieves an associative array of primary keys.
     *
     * @param bool $dirty
     * @return array
     */
    protected function _getPrimaryKey($dirty = true)
    {
        $primary = array_flip($this->_primary);
        if ($dirty) {
            return array_intersect_key($this->_data, $primary);
        } else {
            return array_intersect_key($this->_cleanData, $primary);
        }
    }

    /**
     * Constructs where statement for retrieving row(s).
     *
     * @return array
     */
    protected function _getWhereQuery($dirty = true)
    {
        $where = array();
        $db = $this->_getTable()->getAdapter();
        $keys = $this->_getPrimaryKey($dirty);

        // retrieve recently updated row using primary keys
        foreach ($keys as $key => $val) {
            $where[] = $db->quoteInto($db->quoteIdentifier($key) . ' = ?', $val);
        }
        
        return $where;
    }

    /**
     * Refreshes properties from the database.
     *
     * @return void
     */
    protected function _refresh()
    {
        $where = $this->_getWhereQuery();
        $row = $this->_getTable()->fetchRow($where);
        
        if (null === $row) {
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception('Cannot refresh row as parent is missing');
        }

        $this->_data = $this->_getTable()->fetchRow($where)->toArray();
        $this->_cleanData = $this->_data;
    }

    /**
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _insert()
    {
    }

    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _update()
    {
    }

    /**
     * Allows pre-delete logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _delete()
    {
    }

    /**
     * Prepares a table reference for lookup.
     *
     * Ensures all reference keys are set and properly formatted.
     *
     * @param Zend_Db_Table_Abstract $parentTable
     * @param string                 $tableClass
     * @param string                 $ruleKey
     * @return array
     */
    protected function _prepareReference(Zend_Db_Table_Abstract $table, $tableClass, $ruleKey)
    {
        $map = $table->getReference($tableClass, $ruleKey);
        
        if (!is_array($map[Zend_Db_Table_Abstract::COLUMNS])) {
            $map[Zend_Db_Table_Abstract::COLUMNS] = (array) $map[Zend_Db_Table_Abstract::COLUMNS];
        }
        
        if (!isset($map[Zend_Db_Table_Abstract::REF_COLUMNS])) {
            $info = $table->info();
            $map[Zend_Db_Table_Abstract::REF_COLUMNS] = $info['primary'];
        }

        if (!is_array($map[Zend_Db_Table_Abstract::REF_COLUMNS])) {
            $map[Zend_Db_Table_Abstract::REF_COLUMNS] = (array) $map[Zend_Db_Table_Abstract::REF_COLUMNS];
        }
        
        return $map;
    }

    /**
     * Query a dependent table to retrieve rows matching the current row.
     *
     * @param string|Zend_Db_Table_Abstract  $dependentTable
     * @param string                         OPTIONAL $ruleKey
     * @return Zend_Db_Table_Rowset_Abstract Query result from $dependentTable
     * @throws Zend_Db_Table_Row_Exception If $dependentTable is not a table or is not loadable.
     */ 
    public function findDependentRowset($dependentTable, $ruleKey = null) 
    { 
        $db = $this->_getTable()->getAdapter();

        if (is_string($dependentTable)) {
            try {
                Zend_Loader::loadClass($dependentTable);
            } catch (Zend_Exception $e) {
                require_once 'Zend/Db/Table/Row/Exception.php';
                throw new Zend_Db_Table_Row_Exception($e->getMessage());
            }
            $dependentTable = new $dependentTable(array('db' => $db));
        }
        if (! $dependentTable instanceof Zend_Db_Table_Abstract) {
            $type = gettype($dependentTable);
            if ($type == 'object') {
                $type = get_class($dependentTable);
            }
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Dependent table must be a Zend_Db_Table_Abstract, but it is $type");
        }
        $dependentTableClass = get_class($dependentTable);

        $map = $this->_prepareReference($dependentTable, $this->_tableClass, $ruleKey);

        for ($i = 0; $i < count($map[Zend_Db_Table_Abstract::COLUMNS]); ++$i) {
            $cond = $db->quoteIdentifier($map[Zend_Db_Table_Abstract::COLUMNS][$i]) . ' = ?';
            $where[$cond] = $this->_data[$map[Zend_Db_Table_Abstract::REF_COLUMNS][$i]];
        }
        return $dependentTable->fetchAll($where);
    } 
 
    /**
     * Query a parent table to retrieve the single row matching the current row.
     *
     * @param string|Zend_Db_Table_Abstract $parentTable
     * @param string                        OPTIONAL $ruleKey
     * @return Zend_Db_Table_Row_Abstract   Query result from $parentTable
     * @throws Zend_Db_Table_Row_Exception If $parentTable is not a table or is not loadable.
     */ 
    public function findParentRow($parentTable, $ruleKey = null) 
    { 
        $db = $this->_getTable()->getAdapter();

        if (is_string($parentTable)) {
            try {
                Zend_Loader::loadClass($parentTable);
            } catch (Zend_Exception $e) {
                require_once 'Zend/Db/Table/Row/Exception.php';
                throw new Zend_Db_Table_Row_Exception($e->getMessage());
            }
            $parentTable = new $parentTable(array('db' => $db));
        }
        if (! $parentTable instanceof Zend_Db_Table_Abstract) {
            $type = gettype($parentTable);
            if ($type == 'object') {
                $type = get_class($parentTable);
            }
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Parent table must be a Zend_Db_Table_Abstract, but it is $type");
        }
        $parentTableClass = get_class($parentTable);

        $map = $this->_prepareReference($this->_getTable(), $parentTableClass, $ruleKey);

        for ($i = 0; $i < count($map[Zend_Db_Table_Abstract::COLUMNS]); ++$i) {
            $cond = $db->quoteIdentifier($map[Zend_Db_Table_Abstract::REF_COLUMNS][$i]) . ' = ?';
            $where[$cond] = $this->_data[$map[Zend_Db_Table_Abstract::COLUMNS][$i]];
        }
        return $parentTable->fetchRow($where);
    } 
 
    /**
     * @param string|Zend_Db_Table_Abstract  $matchTable
     * @param string|Zend_Db_Table_Abstract  $intersectionTable
     * @param string                         OPTIONAL $primaryRefRule
     * @param string                         OPTIONAL $matchRefRule
     * @return Zend_Db_Table_Rowset_Abstract Query result from $matchTable
     * @throws Zend_Db_Table_Row_Exception If $matchTable and $intersectionTable are not a tables or are not loadable.
     */ 
    public function findManyToManyRowset( 
        $matchTable, $intersectionTable, 
        $callerRefRule = null, $matchRefRule = null) 
    { 
        $db = $this->_getTable()->getAdapter();

        if (is_string($intersectionTable)) {
            try {
                Zend_Loader::loadClass($intersectionTable);
            } catch (Zend_Exception $e) {
                require_once 'Zend/Db/Table/Row/Exception.php';
                throw new Zend_Db_Table_Row_Exception($e->getMessage());
            }
            $intersectionTable = new $intersectionTable(array('db' => $db));
        }
        if (! $intersectionTable instanceof Zend_Db_Table_Abstract) {
            $type = gettype($intersectionTable);
            if ($type == 'object') {
                $type = get_class($intersectionTable);
            }
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Intersection table must be a Zend_Db_Table_Abstract, but it is $type");
        }
        $intersectionTableClass = get_class($intersectionTable);

        if (is_string($matchTable)) {
            try {
                Zend_Loader::loadClass($matchTable);
            } catch (Zend_Exception $e) {
                require_once 'Zend/Db/Table/Row/Exception.php';
                throw new Zend_Db_Table_Row_Exception($e->getMessage());
            }
            $matchTable = new $matchTable(array('db' => $db));
        }
        if (! $matchTable instanceof Zend_Db_Table_Abstract) {
            $type = gettype($matchTable);
            if ($type == 'object') {
                $type = get_class($matchTable);
            }
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Match table must be a Zend_Db_Table_Abstract, but it is $type");
        }
        $matchTableClass = get_class($matchTable);

        $interInfo = $intersectionTable->info();
        $interName = $interInfo['name'];
        $matchInfo = $matchTable->info();
        $matchName = $matchInfo['name'];

        $matchMap = $this->_prepareReference($intersectionTable, $matchTableClass, $matchRefRule);

        for ($i = 0; $i < count($matchMap[Zend_Db_Table_Abstract::COLUMNS]); ++$i) {
            $interCol = 'i.' . $db->quoteIdentifier($matchMap[Zend_Db_Table_Abstract::COLUMNS][$i]);
            $matchCol = 'm.' . $db->quoteIdentifier($matchMap[Zend_Db_Table_Abstract::REF_COLUMNS][$i]);
            $joinCond[] = "$interCol = $matchCol";
        }
        $joinCond = implode(' AND ', $joinCond);

        $select = $db->select()
            ->from(array('i' => $interName), array())
            ->join(array('m' => $matchName), $joinCond, '*');

        $callerMap = $this->_prepareReference($intersectionTable, $this->_tableClass, $callerRefRule);

        for ($i = 0; $i < count($callerMap[Zend_Db_Table_Abstract::COLUMNS]); ++$i) {
            $interCol = 'i.' . $db->quoteIdentifier($callerMap[Zend_Db_Table_Abstract::COLUMNS][$i]);
            $value = $this->_data[$callerMap[Zend_Db_Table_Abstract::REF_COLUMNS][$i]];
            $select->where("$interCol = ?", $value);
        }
        $stmt = $select->query();

        $config = array(
            'table' => $matchTable,
            'data'  => $stmt->fetchAll(Zend_Db::FETCH_ASSOC)
        );

        $rowsetClass = $matchTable->getRowsetClass();
        $rowset = new $rowsetClass($config);
        return $rowset;
    } 

    /**
     * Turn magic function calls into non-magic function calls
     * to the above methods.
     *
     * @return Zend_Db_Table_Row_Abstract|Zend_Db_Table_Rowset_Abstract
     * @throws Zend_Db_Table_Row_Exception If an invalid method is called.
     */ 
    protected function __call($method, $args) 
    { 

        /**
         * Recognize methods for Has-Many cases: 
         * findParent<Class>() 
         * findParent<Class>By<Rule>() 
         */
        if (preg_match('/^findParent(\w+)(?:By(\w+))?$/', $method, $matches)) {
            $class    = $matches[1];
            $ruleKey1 = isset($matches[2]) ? $matches[2] : null;
            return $this->findParentRow($class, $ruleKey1); 
        }

        /**
         * Recognize methods for Many-to-Many cases: 
         * find<Class1>Via<Class2>() 
         * find<Class1>Via<Class2>By<Rule>() 
         * find<Class1>Via<Class2>By<Rule1>And<Rule2>() 
         */
        if (preg_match('/^find(\w+)Via(\w+)(?:By(\w+)(?:And(\w+))?)?$/', $method, $matches)) {
            $class    = $matches[1];
            $viaClass = $matches[2];
            $ruleKey1 = isset($matches[3]) ? $matches[3] : null;
            $ruleKey2 = isset($matches[4]) ? $matches[4] : null;
            return $this->findManyToManyRowset($class, $viaClass, $ruleKey1, $ruleKey2); 
        }

        /**
         * Recognize methods for Belongs-To cases: 
         * find<Class>() 
         * find<Class>By<Rule>() 
         */
        if (preg_match('/^find(\w+)(?:By(\w+))?$/', $method, $matches)) {
            $class    = $matches[1];
            $ruleKey1 = isset($matches[2]) ? $matches[2] : null;
            return $this->findDependentRowset($class, $ruleKey1); 
        }

        require_once 'Zend/Db/Table/Row/Exception.php';
        throw new Zend_Db_Table_Row_Exception("Unrecognized method '$method()'");
    } 

}
