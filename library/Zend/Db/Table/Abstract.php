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
 * @version    $Id: Abstract.php 4697 2007-05-03 21:23:16Z bkarwin $
 */

/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * @see Zend_Db
 */
require_once 'Zend/Db.php';

/**
 * Class for SQL table interface.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_Abstract
{

    const SCHEMA           = 'schema';
    const NAME             = 'name';
    const PRIMARY          = 'primary';
    const COLS             = 'cols';
    const METADATA         = 'metadata';
    const METADATA_CACHE   = 'metadataCache';
    const ROW_CLASS        = 'rowClass';
    const ROWSET_CLASS     = 'rowsetClass';
    const REFERENCE_MAP    = 'referenceMap';
    const DEPENDENT_TABLES = 'dependentTables';
    const SEQUENCE         = 'sequence';

    const COLUMNS          = 'columns';
    const REF_TABLE_CLASS  = 'refTableClass';
    const REF_COLUMNS      = 'refColumns';
    const ON_DELETE        = 'onDelete';
    const ON_UPDATE        = 'onUpdate';

    const CASCADE          = 'cascade';
    const RESTRICT         = 'restrict';
    const SET_NULL         = 'setNull';

    /**
     * Default Zend_Db_Adapter_Abstract object.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected static $_defaultDb;

    /**
     * Default cache for information provided by the adapter's describeTable() method.
     *
     * @var Zend_Cache_Core
     */
    protected static $_defaultMetadataCache = null;

    /**
     * Zend_Db_Adapter_Abstract object.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * The schema name (default null means current schema)
     *
     * @var array
     */
    protected $_schema = null;

    /**
     * The table name derived from the class name (underscore format).
     *
     * @var array
     */
    protected $_name;

    /**
     * The table column names derived from Zend_Db_Adapter_Abstract::describeTable().
     *
     * @var array
     */
    protected $_cols;

    /**
     * The primary key column or columns.
     * A compound key should be declared as an array.
     * You may declare a single-column primary key 
     * as a string.
     *
     * @var mixed
     */
    protected $_primary = null;

    /**
     * Define the logic for new values in the primary key.
     * May be a string, boolean true, or boolean false.
     *
     * @var mixed
     */
    protected $_sequence = true;

    /**
     * Information provided by the adapter's describeTable() method.
     *
     * @var array
     */
    protected $_metadata = array();

    /**
     * Cache for information provided by the adapter's describeTable() method.
     *
     * @var Zend_Cache_Core
     */
    protected $_metadataCache = null;

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Zend_Db_Table_Row';

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Zend_Db_Table_Rowset';

    /**
     * Associative array map of declarative referential integrity rules.
     * This array has one entry per foreign key in the current table.
     * Each key is a mnemonic name for one reference rule.
     *
     * Each value is also an associative array, with the following keys:
     * - columns       = array of names of column(s) in the child table.
     * - refTableClass = class name of the parent table.
     * - refColumns    = array of names of column(s) in the parent table,
     *                   in the same order as those in the 'columns' entry.
     * - onDelete      = "cascade" means that a delete in the parent table also
     *                   causes a delete of referencing rows in the child table.
     * - onUpdate      = "cascade" means that an update of primary key values in
     *                   the parent table also causes an update of referencing
     *                   rows in the child table.
     *
     * @var array
     */
    protected $_referenceMap = array();

    /**
     * Simple array of class names of tables that are "children" of the current
     * table, in other words tables that contain a foreign key to this one.
     * Array elements are not table names; they are class names of classes that
     * extend Zend_Db_Table_Abstract.
     *
     * @var array
     */
    protected $_dependentTables = array();

    /**
     * Constructor.
     *
     * Supported params for $config are:
     * - db              = user-supplied instance of database connector,
     *                     or key name of registry instance.
     * - name            = table name.
     * - primary         = string or array of primary key(s).
     * - rowClass        = row class name.
     * - rowsetClass     = rowset class name.
     * - referenceMap    = array structure to declare relationship
     *                     to parent tables.
     * - dependentTables = array of child tables.
     * - metadataCache   = cache for information from adapter describeTable().
     *
     * @param  array $config Array of user-specified config options.
     * @return void
     */
    public function __construct(array $config = array())
    {
        // set a custom Zend_Db_Adapter connection
        if (isset($config['db'])) {

            // convenience variable
            $db = $config['db'];

            // use an object from the registry?
            if (is_string($db)) {
                $db = Zend_Registry::get($db);
            }

            // save the connection
            $this->_db = $db;
        }

        // set default table name if supplied
        if (isset($config[self::NAME])) {
            $this->_name = $config[self::NAME];
        }

        // set primary key name if supplied
        if (isset($config[self::PRIMARY])) {
            $this->_primary = (array) $config[self::PRIMARY];
        }

        // set default row classname if supplied
        if (isset($config[self::ROW_CLASS])) {
            $this->setRowClass($config[self::ROW_CLASS]);
        }

        // set default rowset classname if supplied
        if (isset($config[self::ROWSET_CLASS])) {
            $this->setRowsetClass($config[self::ROWSET_CLASS]);
        }

        if (isset($config[self::REFERENCE_MAP])) {
            $this->setReferences($config[self::REFERENCE_MAP]);
        }

        if (isset($config[self::DEPENDENT_TABLES])) {
            $this->setDependentTables($config[self::DEPENDENT_TABLES]);
        }

        if (isset($config[self::METADATA_CACHE])) {
            $this->_setMetadataCache($config[self::METADATA_CACHE]);
        }

        if (isset($config[self::SEQUENCE])) {
            $this->_setSequence($config[self::SEQUENCE]);
        }

        // continue with automated setup
        $this->_setup();
    }

    /**
     * @param  string $classname
     * @return Zend_Db_Table_Abstract Provides a fluent interface
     */
    public function setRowClass($classname)
    {
        $this->_rowClass = (string) $classname;

        return $this;
    }

    /**
     * @return string
     */
    public function getRowClass()
    {
        return $this->_rowClass;
    }

    /**
     * @param  string $classname
     * @return Zend_Db_Table_Abstract Provides a fluent interface
     */
    public function setRowsetClass($classname)
    {
        $this->_rowsetClass = (string) $classname;

        return $this;
    }

    /**
     * @return string
     */
    public function getRowsetClass()
    {
        return $this->_rowsetClass;
    }

    /**
     * @param array $referenceMap
     * @return Zend_Db_Table_Abstract Provides a fluent interface
     */
    public function setReferences(array $referenceMap)
    {
        $this->_referenceMap = $referenceMap;

        return $this;
    }

    /**
     * @param string $tableClassname
     * @param string $ruleKey OPTIONAL
     * @return array
     * @throws Zend_Db_Table_Exception
     */
    public function getReference($tableClassname, $ruleKey = null)
    {
        $thisClass = get_class($this);
        if ($ruleKey !== null) {
            if (!isset($this->_referenceMap[$ruleKey])) {
                require_once "Zend/Db/Table/Exception.php";
                throw new Zend_Db_Table_Exception("No reference rule \"$ruleKey\" from table $thisClass to table $tableClassname");
            }
            if ($this->_referenceMap[$ruleKey][self::REF_TABLE_CLASS] != $tableClassname) {
                require_once "Zend/Db/Table/Exception.php";
                throw new Zend_Db_Table_Exception("Reference rule \"$ruleKey\" does not reference table $tableClassname");
            }
            return $this->_referenceMap[$ruleKey];
        }
        foreach ($this->_referenceMap as $reference) {
            if ($reference[self::REF_TABLE_CLASS] == $tableClassname) {
                return $reference;
            }
        }
        require_once "Zend/Db/Table/Exception.php";
        throw new Zend_Db_Table_Exception("No reference from table $thisClass to table $tableClassname");
    }

    /**
     * @param  array $dependentTables
     * @return Zend_Db_Table_Abstract Provides a fluent interface
     */
    public function setDependentTables(array $dependentTables)
    {
        $this->_dependentTables = $dependentTables;

        return $this;
    }

    /**
     * @return array
     */
    public function getDependentTables()
    {
        return $this->_dependentTables;
    }

    /**
     * Sets the default Zend_Db_Adapter_Abstract for all Zend_Db_Table objects.
     *
     * @param  Zend_Db_Adapter_Abstract
     * @return void
     */
    public static final function setDefaultAdapter(Zend_Db_Adapter_Abstract $db)
    {
        Zend_Db_Table_Abstract::$_defaultDb = $db;
    }

    /**
     * Gets the default Zend_Db_Adapter_Abstract for all Zend_Db_Table objects.
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public static final function getDefaultAdapter()
    {
        return self::$_defaultDb;
    }

    /**
     * Gets the Zend_Db_Adapter_Abstract for this particular Zend_Db_Table object.
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public final function getAdapter()
    {
        return $this->_db;
    }

    /**
     * Sets the default metadata cache for information returned by Zend_Db_Adapter_Abstract::describeTable().
     *
     * If $defaultMetadataCache is null, then no metadata cache is used by default.
     *
     * @param  Zend_Cache_Core $defaultMetadataCache
     * @return void
     */
    public static function setDefaultMetadataCache(Zend_Cache_Core $defaultMetadataCache = null)
    {
        self::$_defaultMetadataCache = $defaultMetadataCache;
    }

    /**
     * Gets the default metadata cache for information returned by Zend_Db_Adapter_Abstract::describeTable().
     *
     * @return Zend_Cache_Core|null
     */
    public static function getDefaultMetadataCache()
    {
        return self::$_defaultMetadataCache;
    }

    /**
     * Sets the metadata cache for information returned by Zend_Db_Adapter_Abstract::describeTable().
     *
     * If $metadataCache is null, then no metadata cache is used. Since there is no opportunity to reload metadata
     * after instantiation, this method need not be public, particularly because that it would have no effect
     * results in unnecessary API complexity. To configure the metadata cache, use the metadataCache configuration
     * option for the class constructor upon instantiation.
     *
     * @param  Zend_Cache_Core $metadataCache
     * @return Zend_Db_Table_Adapter_Abstract Provides a fluent interface
     */
    protected function _setMetadataCache(Zend_Cache_Core $metadataCache = null)
    {
        $this->_metadataCache = $metadataCache;

        return $this;
    }

    /**
     * Sets the sequence member, which defines the behavior for generating
     * primary key values in new rows.
     * - If this is a string, then the string names the sequence object.
     * - If this is boolean true, then the key uses an auto-incrementing
     *   or identity mechanism.
     * - If this is boolean false, then the key is user-defined.
     *   Use this for natural keys, for example.
     *
     * @param mixed $sequence
     * @return Zend_Db_Table_Adapter_Abstract Provides a fluent interface
     */
    protected function _setSequence($sequence)
    {
        $this->_sequence = $sequence;

        return $this;
    }

    /**
     * Gets the metadata cache for information returned by Zend_Db_Adapter_Abstract::describeTable().
     *
     * @return Zend_Cache_Core|null
     */
    public function getMetadataCache()
    {
        return $this->_metadataCache;
    }

    /**
     * Turnkey for initialization of a table object.
     * Calls other protected methods for individual tasks, to make it easier
     * for a subclass to override part of the setup logic.
     *
     * @return void
     * @throws Zend_Db_Table_Exception
     */
    protected function _setup()
    {
        $this->_setupDatabaseAdapter();
        $this->_setupTableName();
        $this->_setupMetadata();
        $this->_setupPrimaryKey();
    }

    /**
     * Initialize database adapter.
     *
     * @return void
     * @throws Zend_Db_Table_Exception
     */
    protected function _setupDatabaseAdapter()
    {
        // get the database adapter
        if (! $this->_db) {
            $this->_db = self::getDefaultAdapter();
        }

        if (! $this->_db instanceof Zend_Db_Adapter_Abstract) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception('No object of type Zend_Db_Adapter_Abstract has been specified');
        }
    }

    /**
     * Initialize table name.
     * If the table name is not set in the class definition,
     * use the class name itself as the table name.
     *
     * @return void
     * @throws Zend_Db_Table_Exception
     */
    protected function _setupTableName()
    {
        if (! $this->_name) {
            $this->_name = get_class($this);
        }
    }

    /**
     * Initializes metadata.
     *
     * If metadata cannot be loaded from cache, adapter's describeTable() method is called to discover metadata
     * information. Returns true if and only if the metadata are loaded from cache.
     *
     * @return boolean
     * @throws Zend_Db_Table_Exception
     */
    protected function _setupMetadata()
    {
        if (strpos($this->_name, '.')) {
            list($schemaName, $tableName) = explode('.', $this->_name);
            $this->_schema = $schemaName;
            $this->_name = $tableName;
        } else {
            $schemaName = $this->_schema;
            $tableName = $this->_name;
        }

        // Assume that metadata will be loaded from cache
        $isMetadataFromCache = true;

        // If $this has no metadata cache but the class has a default metadata cache
        if (null === $this->_metadataCache && null !== self::$_defaultMetadataCache) {
            // Make $this use the default metadata cache of the class
            $this->_setMetadataCache(self::$_defaultMetadataCache);
        }

        // If $this has a metadata cache
        if (null !== $this->_metadataCache) {
            // Define the cache identifier where the metadata are saved
            $cacheId = md5("$schemaName.$tableName");
        }

        // If $this has no metadata cache or metadata cache misses
        if (null === $this->_metadataCache || !($metadata = $this->_metadataCache->load($cacheId))) {
            // Metadata are not loaded from cache
            $isMetadataFromCache = false;
            // Fetch metadata from the adapter's describeTable() method
            $metadata = $this->_db->describeTable($tableName, $schemaName);
            // If $this has a metadata cache, then cache the metadata
            if (null !== $this->_metadataCache && !$this->_metadataCache->save($metadata, $cacheId)) {
                /**
                 * @see Zend_Db_Table_Exception
                 */
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception('Failed saving metadata to metadataCache');
            }
        }

        // Assign the metadata to $this
        $this->_metadata = $metadata;

        // Update the columns
        $this->_cols = array_keys($this->_metadata);

        // Return whether the metadata were loaded from cache
        return $isMetadataFromCache;
    }

    /**
     * Initialize primary key from metadata.
     * If $_primary is not defined, discover primary keys
     * from the information returned by describeTable().
     *
     * @return void
     * @throws Zend_Db_Table_Exception
     */
    protected function _setupPrimaryKey()
    {
        if (!$this->_primary) {
            foreach ($this->_metadata as $col) {
                if ($col['PRIMARY']) {
                    $this->_primary[ $col['PRIMARY_POSITION'] ] = $col['COLUMN_NAME'];
                }
            }
        }

        if (! array_intersect((array) $this->_primary, $this->_cols) == (array) $this->_primary) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Primary key column(s) ("
                . implode(',', (array) $this->_primary)
                . ") are not columns in this table ("
                . implode(',', $this->_cols)
                . ")");
        }

        $primary = (array) $this->_primary;
        $pk0 = current($primary);
        if ($this->_sequence === true && get_class($this->_db) == 'Zend_Db_Adapter_Pdo_Pgsql') {
            $this->_sequence = "{$this->_name}_{$pk0}_seq";
        }
    }

    /**
     * Returns a normalized version of the reference map
     *
     * @return array
     */
    protected function _getReferenceMapNormalized()
    {
        $referenceMapNormalized = array();

        foreach ($this->_referenceMap as $rule => $map) {

            $referenceMapNormalized[$rule] = array();

            foreach ($map as $key => $value) {
                switch ($key) {

                    // normalize COLUMNS and REF_COLUMNS to arrays
                    case self::COLUMNS:
                    case self::REF_COLUMNS:
                        if (!is_array($value)) {
                            $referenceMapNormalized[$rule][$key] = array($value);
                        } else {
                            $referenceMapNormalized[$rule][$key] = $value;
                        }
                        break;

                    // other values are copied as-is
                    default:
                        $referenceMapNormalized[$rule][$key] = $value;
                        break;
                }
            }
        }

        return $referenceMapNormalized;
    }

    /**
     * Returns table information.
     *
     * @return array
     */
    public function info()
    {
        return array(
            self::SCHEMA           => $this->_schema,
            self::NAME             => $this->_name,
            self::COLS             => (array) $this->_cols,
            self::PRIMARY          => (array) $this->_primary,
            self::METADATA         => $this->_metadata,
            self::ROW_CLASS        => $this->_rowClass,
            self::ROWSET_CLASS     => $this->_rowsetClass,
            self::REFERENCE_MAP    => $this->_referenceMap,
            self::DEPENDENT_TABLES => $this->_dependentTables,
            self::SEQUENCE         => $this->_sequence
        );
    }

    /**
     * Inserts a new row.
     *
     * @param  array  $data  Column-value pairs.
     * @return mixed         The primary key of the row inserted.
     */
    public function insert(array $data)
    {
        /**
         * Zend_Db_Table assumes that if you have a compound primary key
         * and one of the columns in the key uses a sequence,
         * it's the _first_ column in the compound key.
         */
        $primary = (array) $this->_primary;
        $pk0 = current($primary);

        /**
         * If this table uses a database sequence object and the data does not
         * specify a value, then get the next ID from the sequence and add it
         * to the row.  We assume that only the first column in a compound
         * primary key takes a value from a sequence.
         */
        if (is_string($this->_sequence) && !isset($data[$pk0])) {
            $data[$pk0] = $this->_db->nextSequenceId($this->_sequence);
        }

        /**
         * INSERT the new row.
         */
        $this->_db->insert($this->_name, $data);

        if (isset($data[$pk0])) {
            /**
             * Return the primary key value or array of values(s) if the
             * primary key is compound.  This handles the case of natural keys
             * and sequence-driven keys.  This also covers the case of
             * auto-increment keys when the user specifies a value, thus
             * overriding the auto-increment logic.
             */
            if (count($primary) == 1) {
                return $data[$pk0];
            } else {
                return array_intersect_key($data, array_flip($primary));
            }
        }

        if ($this->_sequence === true) {
            /**
             * Return the most recent ID generated by an auto-increment
             * or IDENTITY column.
             */
            return $this->_db->lastInsertId();
        } 

        /**
         * The last case:  the user did not specify a value for the primary
         * key, nor is this table class declared to use an auto-increment key.
         * Since the insert did not fail, we can assume this is one of the edge
         * cases, which may include:
         * - the table has no primary key defined;
         * - the database table uses a trigger to set a primary key value;
         * - the RDBMS permits primary keys to be NULL or have a value set
         *   to the column's DEFAULT
         */
        return null;
    }

    /**
     * Updates existing rows.
     *
     * @param  array        $data  Column-value pairs.
     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     * @return int          The number of rows updated.
     */
    public function update(array $data, $where)
    {
        return $this->_db->update($this->_name, $data, $where);
    }

    /**
     * Called by a row object for the parent table's class during save() method.
     *
     * @param  string $parentTableClassname
     * @param  array  $oldPrimaryKey
     * @param  array  $newPrimaryKey
     * @return int
     */
    public function _cascadeUpdate($parentTableClassname, array $oldPrimaryKey, array $newPrimaryKey)
    {
        $rowsAffected = 0;
        foreach ($this->_referenceMap as $rule => $map) {
            if ($map[self::REF_TABLE_CLASS] == $parentTableClassname && isset($map[self::ON_UPDATE])) {
                switch ($map[self::ON_UPDATE]) {
                    case self::CASCADE:
                        $newRefs = array();
                        for ($i = 0; $i < count($map[self::COLUMNS]); ++$i) {
                            if (array_key_exists($map[self::REF_COLUMNS][$i], $newPrimaryKey)) {
                                $newRefs[$map[self::COLUMNS][$i]] = $newPrimaryKey[$map[self::REF_COLUMNS][$i]];
                            }
                            $where[] = $this->_db->quoteInto(
                                $this->_db->quoteIdentifier($map[self::COLUMNS][$i]) . ' = ?',
                                $oldPrimaryKey[$map[self::REF_COLUMNS][$i]]
                            );
                        }
                        $rowsAffected += $this->update($newRefs, $where);
                        break;
                    default:
                        // no action
                        break;
                }
            }
        }
        return $rowsAffected;
    }

    /**
     * Deletes existing rows.
     *
     * @param  array|string $where SQL WHERE clause(s).
     * @return int          The number of rows deleted.
     */
    public function delete($where)
    {
        return $this->_db->delete($this->_name, $where);
    }

    /**
     * Called by parent table's class during delete() method.
     *
     * @param  string $parentTableClassname
     * @param  array  $primaryKey
     * @return int    Number of affected rows
     */
    public function _cascadeDelete($parentTableClassname, array $primaryKey)
    {
        $rowsAffected = 0;
        foreach ($this->_getReferenceMapNormalized() as $rule => $map) {
            if ($map[self::REF_TABLE_CLASS] == $parentTableClassname && isset($map[self::ON_DELETE])) {
                switch ($map[self::ON_DELETE]) {
                    case self::CASCADE:
                        for ($i = 0; $i < count($map[self::COLUMNS]); ++$i) {
                            $where[] = $this->_db->quoteInto(
                                $this->_db->quoteIdentifier($map[self::COLUMNS][$i]) . ' = ?',
                                $primaryKey[$map[self::REF_COLUMNS][$i]]
                            );
                        }
                        $rowsAffected += $this->delete($where);
                        break;
                    default:
                        // no action
                        break;
                }
            }
        }
        return $rowsAffected;
    }

    /**
     * Fetches rows by primary key.
     * The arguments specify the primary key values.
     * If the table has a multi-column primary key, you must
     * pass as many arguments as the count of column in the
     * primary key.
     *
     * To find multiple rows by primary key, the argument
     * should be an array.  If the table has a multi-column
     * primary key, all arguments must be arrays with the
     * same number of elements.
     *
     * The find() method always returns a Rowset object,
     * even if only one row was found.
     *
     * @param  mixed                         The value(s) of the primary key.
     * @return Zend_Db_Table_Rowset_Abstract Row(s) matching the criteria.
     * @throws Zend_Db_Table_Exception
     */
    public function find()
    {
        $args = func_get_args();
        $keyNames = array_values((array) $this->_primary);

        if (empty($args)) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("No value(s) specified for the primary key");
        }

        if (count($args) != count($keyNames)) {
            require_once 'Zend/Db/Table/Exception.php';
            throw new Zend_Db_Table_Exception("Missing value(s) for the primary key");
        }

        $whereList = array();
        $numberTerms = 0;
        foreach ($args as $keyPosition => $keyValues) {
            // Coerce the values to an array.
            // Don't simply typecast to array, because the values
            // might be Zend_Db_Expr objects.
            if (!is_array($keyValues)) {
                $keyValues = array($keyValues);
            }
            if ($numberTerms == 0) {
                $numberTerms = count($keyValues);
            } else if (count($keyValues) != $numberTerms) {
                require_once 'Zend/Db/Table/Exception.php';
                throw new Zend_Db_Table_Exception("Missing value(s) for the primary key");
            }
            for ($i = 0; $i < count($keyValues); ++$i) {
                $whereList[$i][$keyPosition] = $keyValues[$i];
            }
        }

        $whereClause = null;
        if (count($whereList)) {
            $whereOrTerms = array();
            foreach ($whereList as $keyValueSets) {
                $whereAndTerms = array();
                foreach ($keyValueSets as $keyPosition => $keyValue) {
                    $whereAndTerms[] = $this->_db->quoteInto(
                        $this->_db->quoteIdentifier($keyNames[$keyPosition]) . ' = ?',
                        $keyValue
                    );
                }
                $whereOrTerms[] = '(' . implode(' AND ', $whereAndTerms) . ')';
            }
            $whereClause = '(' . implode(' OR ', $whereOrTerms) . ')';
        }

        return $this->fetchAll($whereClause);
    }

    /**
     * Fetches all rows.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array $where            OPTIONAL An SQL WHERE clause.
     * @param string|array $order            OPTIONAL An SQL ORDER clause.
     * @param int          $count            OPTIONAL An SQL LIMIT count.
     * @param int          $offset           OPTIONAL An SQL LIMIT offset.
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $data  = array(
            'table'    => $this,
            'data'     => $this->_fetch($where, $order, $count, $offset),
            'rowClass' => $this->_rowClass,
            'stored'   => true
        );

        Zend_Loader::loadClass($this->_rowsetClass);
        return new $this->_rowsetClass($data);
    }

    /**
     * Fetches one row in an object of type Zend_Db_Table_Row_Abstract,
     * or returns Boolean false if no row matches the specified criteria.
     *
     * @param string|array $where         OPTIONAL An SQL WHERE clause.
     * @param string|array $order         OPTIONAL An SQL ORDER clause.
     * @return Zend_Db_Table_Row_Abstract The row results per the
     *     Zend_Db_Adapter fetch mode, or null if no row found.
     */
    public function fetchRow($where = null, $order = null)
    {
        $keys    = array_values((array) $this->_primary);
        $vals    = array_fill(0, count($keys), null);
        $primary = array_combine($keys, $vals);

        $rows = $this->_fetch($where, $order, 1);

        if (count($rows) == 0) {
            return null;
        }

        $data = array(
            'table'   => $this,
            'data'    => $rows[0],
            'stored'  => true
        );

        Zend_Loader::loadClass($this->_rowClass);
        return new $this->_rowClass($data);
    }

    /**
     * Fetches a new blank row (not from the database).
     *
     * @return Zend_Db_Table_Row_Abstract
     * @deprecated since 0.9.3 - use createRow() instead.
     */
    public function fetchNew()
    {
        return $this->createRow();
    }

    /**
     * Fetches a new blank row (not from the database).
     *
     * @param  array $data OPTIONAL data to populate in the new row.
     * @return Zend_Db_Table_Row_Abstract
     */
    public function createRow(array $data = array())
    {
        $defaults = array_combine($this->_cols, array_fill(0, count($this->_cols), null));
        $keys = array_flip($this->_cols);
        $data = array_intersect_key($data, $keys);
        $data = array_merge($defaults, $data);

        $config = array(
            'table'   => $this,
            'data'    => $data,
            'stored'  => false
        );

        Zend_Loader::loadClass($this->_rowClass);
        return new $this->_rowClass($config);
    }

    /**
     * Support method for fetching rows.
     *
     * @param  string|array $where  OPTIONAL An SQL WHERE clause.
     * @param  string|array $order  OPTIONAL An SQL ORDER clause.
     * @param  int          $count  OPTIONAL An SQL LIMIT count.
     * @param  int          $offset OPTIONAL An SQL LIMIT offset.
     * @return array The row results, in FETCH_ASSOC mode.
     */
    protected function _fetch($where = null, $order = null, $count = null, $offset = null)
    {
        // selection tool
        $select = $this->_db->select();

        // the FROM clause
        $select->from($this->_name, $this->_cols);

        // the WHERE clause
        $where = (array) $where;
        foreach ($where as $key => $val) {
            // is $key an int?
            if (is_int($key)) {
                // $val is the full condition
                $select->where($val);
            } else {
                // $key is the condition with placeholder,
                // and $val is quoted into the condition
                $select->where($key, $val);
            }
        }

        // the ORDER clause
        if (!is_array($order)) {
            $order = array($order);
        }
        foreach ($order as $val) {
            $select->order($val);
        }

        // the LIMIT clause
        $select->limit($count, $offset);

        // return the results
        $stmt = $this->_db->query($select);
        $data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        return $data;
    }

}
