<?php
/**
 * @internal
 */
class Kwf_Db_Table
{
    private $_dao;
    protected $_rowClass = 'Kwf_Db_Table_Row';
    protected $_rowsetClass = 'Kwf_Db_Table_Rowset';

    //_setupAdapter nicht ausführen, wir machen das besser lazy in _setupDatabaseAdapter
    protected function _setAdapter($db)
    {
        $this->_db = $db;
        return $this;
    }

    protected function _setupDatabaseAdapter()
    {
        //instead of setDefaultAdapter - this one lazy loads
        if (! $this->_db) {
            $this->_db = Kwf_Registry::get('db');
        } else if (is_string($this->_db)) {
            $this->_db = Kwf_Registry::get('dao')->getDb($this->_db);
        }
    }

    public function setDao($dao)
    {
        $this->_dao = $dao;
    }

    public function getDao()
    {
        return $this->_dao;
    }

    public function select()
    {
        return new Kwf_Db_Table_Select($this);
    }

    protected function _fetch(Kwf_Db_Table_Select $select)
    {
        if ($this->_db instanceof Zend_Db_Adapter_Pdo_Abstract) {
            //Overridden for better performance if Pdo Adatper is used
            //avoids parsing sql in Zend_Db_Statement::_stripQuoted which is slow
            $sql = $select->assemble();
            $conn = $this->_db->getConnection();
            $queryId = $this->_db->getProfiler()->queryStart($sql);
            $stmt = $conn->query($sql);
            $this->_db->getProfiler()->queryEnd($queryId);
            $data = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;
        } else {
            $stmt = $this->_db->query($select);
            $data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
            return $data;
        }
    }















































    const ADAPTER          = 'db';
    const SCHEMA           = 'schema';
    const NAME             = 'name';
    const PRIMARY          = 'primary';
    const COLS             = 'cols';
    const METADATA         = 'metadata';
    const SEQUENCE         = 'sequence';

    const COLUMNS          = 'columns';

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
     * The table name.
     *
     * @var string
     */
    protected $_name = null;

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
     * If your primary key is a compound key, and one of the columns uses
     * an auto-increment or sequence-generated value, set _identity
     * to the ordinal index in the $_primary array for that column.
     * Note this index is the position of the column in the primary key,
     * not the position of the column in the table.  The primary key
     * array is 1-based.
     *
     * @var integer
     */
    protected $_identity = 1;

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
     * Constructor.
     *
     * Supported params for $config are:
     * - db              = user-supplied instance of database connector,
     *                     or key name of registry instance.
     * - name            = table name.
     * - primary         = string or array of primary key(s).
     *
     * @param  mixed $config Array of user-specified config options, or just the Db Adapter.
     * @return void
     */
    public function __construct($config = array())
    {
        /**
         * Allow a scalar argument to be the Adapter object or Registry key.
         */
        if (!is_array($config)) {
            $config = array(self::ADAPTER => $config);
        }

        if ($config) {
            $this->setOptions($config);
        }

        $this->_setup();
        $this->init();
    }

    /**
     * setOptions()
     *
     * @param array $options
     * @return Kwf_Db_Table
     */
    public function setOptions(Array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case self::ADAPTER:
                    $this->_setAdapter($value);
                    break;
                case self::SCHEMA:
                    $this->_schema = (string) $value;
                    break;
                case self::NAME:
                    $this->_name = (string) $value;
                    break;
                case self::PRIMARY:
                    $this->_primary = (array) $value;
                    break;
                case self::SEQUENCE:
                    $this->_setSequence($value);
                    break;
                default:
                    // ignore unrecognized configuration directive
                    break;
            }
        }

        return $this;
    }

    /**
     * Gets the Zend_Db_Adapter_Abstract for this particular Kwf_Db_Table object.
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_db;
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
     * @return Kwf_Db_Table Provides a fluent interface
     */
    protected function _setSequence($sequence)
    {
        $this->_sequence = $sequence;

        return $this;
    }

    /**
     * Turnkey for initialization of a table object.
     * Calls other protected methods for individual tasks, to make it easier
     * for a subclass to override part of the setup logic.
     *
     * @return void
     */
    protected function _setup()
    {
        $this->_setupDatabaseAdapter();
        $this->_setupTableName();
    }

    /**
     * Initialize table and schema names.
     *
     * If the table name is not set in the class definition,
     * use the class name itself as the table name.
     *
     * A schema name provided with the table name (e.g., "schema.table") overrides
     * any existing value for $this->_schema.
     *
     * @return void
     */
    protected function _setupTableName()
    {
        if (strpos($this->_name, '.')) {
            list($this->_schema, $this->_name) = explode('.', $this->_name);
        }
    }

    /**
     * Initializes metadata.
     *
     * If metadata cannot be loaded from cache, adapter's describeTable() method is called to discover metadata
     * information. Returns true if and only if the metadata are loaded from cache.
     *
     * @return boolean
     * @throws Kwf_Exception
     */
    protected function _setupMetadata()
    {
        if (count($this->_metadata) > 0) {
            return true;
        }

        // Assume that metadata will be loaded from cache
        $isMetadataFromCache = true;

        // Define the cache identifier where the metadata are saved

        //get db configuration
        $dbConfig = $this->_db->getConfig();

        $port = isset($dbConfig['options']['port'])
                ? ':'.$dbConfig['options']['port']
                : (isset($dbConfig['port'])
                ? ':'.$dbConfig['port']
                : null);

        $host = isset($dbConfig['options']['host'])
                ? ':'.$dbConfig['options']['host']
                : (isset($dbConfig['host'])
                ? ':'.$dbConfig['host']
                : null);

        // Define the cache identifier where the metadata are saved
        $cacheId = 'dbtbl_'.md5( // port:host/dbname:schema.table (based on availabilty)
                $port . $host . '/'. $dbConfig['dbname'] . ':'
                . $this->_schema. '.' . $this->_name
        );

        // If $this has no metadata cache or metadata cache misses
        if (!($metadata = Kwf_Cache_SimpleStatic::fetch($cacheId))) {
            // Metadata are not loaded from cache
            $isMetadataFromCache = false;
            // Fetch metadata from the adapter's describeTable() method
            $metadata = $this->_db->describeTable($this->_name, $this->_schema);
            // If $this has a metadata cache, then cache the metadata
            Kwf_Cache_SimpleStatic::add($cacheId, $metadata);
        }

        // Assign the metadata to $this
        $this->_metadata = $metadata;

        // Return whether the metadata were loaded from cache
        return $isMetadataFromCache;
    }

    /**
     * Retrieve table columns
     *
     * @return array
     */
    protected function _getCols()
    {
        if (null === $this->_cols) {
            $this->_setupMetadata();
            $this->_cols = array_keys($this->_metadata);
        }
        return $this->_cols;
    }

    /**
     * Initialize primary key from metadata.
     * If $_primary is not defined, discover primary keys
     * from the information returned by describeTable().
     *
     * @return void
     * @throws Kwf_Exception
     */
    protected function _setupPrimaryKey()
    {
        if (!$this->_primary) {
            $this->_setupMetadata();
            $this->_primary = array();
            foreach ($this->_metadata as $col) {
                if ($col['PRIMARY']) {
                    $this->_primary[ $col['PRIMARY_POSITION'] ] = $col['COLUMN_NAME'];
                    if ($col['IDENTITY']) {
                        $this->_identity = $col['PRIMARY_POSITION'];
                    }
                }
            }
            // if no primary key was specified and none was found in the metadata
            // then throw an exception.
            if (empty($this->_primary)) {
                throw new Kwf_Exception("A table must have a primary key, but none was found for table '{$this->_name}'");
            }
        } else if (!is_array($this->_primary)) {
            $this->_primary = array(1 => $this->_primary);
        } else if (isset($this->_primary[0])) {
            array_unshift($this->_primary, null);
            unset($this->_primary[0]);
        }

        $cols = $this->_getCols();
        if (! array_intersect((array) $this->_primary, $cols) == (array) $this->_primary) {
            throw new Kwf_Exception("Primary key column(s) ("
                . implode(',', (array) $this->_primary)
                . ") are not columns in this table ("
                . implode(',', $cols)
                . ")");
        }

        $primary    = (array) $this->_primary;
        $pkIdentity = $primary[(int) $this->_identity];

        /**
         * Special case for PostgreSQL: a SERIAL key implicitly uses a sequence
         * object whose name is "<table>_<column>_seq".
         */
        if ($this->_sequence === true && $this->_db instanceof Zend_Db_Adapter_Pdo_Pgsql) {
            $this->_sequence = $this->_db->quoteIdentifier("{$this->_name}_{$pkIdentity}_seq");
            if ($this->_schema) {
                $this->_sequence = $this->_db->quoteIdentifier($this->_schema) . '.' . $this->_sequence;
            }
        }
    }

    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Returns table information.
     *
     * You can elect to return only a part of this information by supplying its key name,
     * otherwise all information is returned as an array.
     *
     * @param  string $key The specific info part to return OPTIONAL
     * @return mixed
     * @throws Kwf_Exception
     */
    public function info($key = null)
    {
        $this->_setupPrimaryKey();

        $info = array(
            self::SCHEMA           => $this->_schema,
            self::NAME             => $this->_name,
            self::COLS             => $this->_getCols(),
            self::PRIMARY          => (array) $this->_primary,
            self::METADATA         => $this->_metadata,
            self::SEQUENCE         => $this->_sequence
        );

        if ($key === null) {
            return $info;
        }

        if (!array_key_exists($key, $info)) {
            throw new Kwf_Exception('There is no table information for the key "' . $key . '"');
        }

        return $info[$key];
    }

    /**
     * Inserts a new row.
     *
     * @param  array  $data  Column-value pairs.
     * @return mixed         The primary key of the row inserted.
     */
    public function insert(array $data)
    {
        $this->_setupPrimaryKey();

        /**
         * Kwf_Db_Table assumes that if you have a compound primary key
         * and one of the columns in the key uses a sequence,
         * it's the _first_ column in the compound key.
         */
        $primary = (array) $this->_primary;
        $pkIdentity = $primary[(int)$this->_identity];


        /**
         * If the primary key can be generated automatically, and no value was
         * specified in the user-supplied data, then omit it from the tuple.
         *
         * Note: this checks for sensible values in the supplied primary key
         * position of the data.  The following values are considered empty:
         *   null, false, true, '', array()
         */
        if (array_key_exists($pkIdentity, $data)) {
            if ($data[$pkIdentity] === null                                        // null
                || $data[$pkIdentity] === ''                                       // empty string
                || is_bool($data[$pkIdentity])                                     // boolean
                || (is_array($data[$pkIdentity]) && empty($data[$pkIdentity]))) {  // empty array
                unset($data[$pkIdentity]);
            }
        }

        /**
         * If this table uses a database sequence object and the data does not
         * specify a value, then get the next ID from the sequence and add it
         * to the row.  We assume that only the first column in a compound
         * primary key takes a value from a sequence.
         */
        if (is_string($this->_sequence) && !isset($data[$pkIdentity])) {
            $data[$pkIdentity] = $this->_db->nextSequenceId($this->_sequence);
        }

        /**
         * INSERT the new row.
         */
        $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
        $this->_db->insert($tableSpec, $data);

        /**
         * Fetch the most recent ID generated by an auto-increment
         * or IDENTITY column, unless the user has specified a value,
         * overriding the auto-increment mechanism.
         */
        if ($this->_sequence === true && !isset($data[$pkIdentity])) {
            $data[$pkIdentity] = $this->_db->lastInsertId();
        }

        /**
         * Return the primary key value if the PK is a single column,
         * else return an associative array of the PK column/value pairs.
         */
        $pkData = array_intersect_key($data, array_flip($primary));
        if (count($primary) == 1) {
            reset($pkData);
            return current($pkData);
        }

        return $pkData;
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
        $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
        return $this->_db->update($tableSpec, $data, $where);
    }

    /**
     * Deletes existing rows.
     *
     * @param  array|string $where SQL WHERE clause(s).
     * @return int          The number of rows deleted.
     */
    public function delete($where)
    {
        $tableSpec = ($this->_schema ? $this->_schema . '.' : '') . $this->_name;
        return $this->_db->delete($tableSpec, $where);
    }

    /**
     * Fetches all rows.
     *
     * Honors the Zend_Db_Adapter fetch mode.
     *
     * @param string|array|Kwf_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Kwf_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Kwf_Db_Table_Rowset The row results per the Zend_Db_Adapter fetch mode.
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        if (!($where instanceof Kwf_Db_Table_Select)) {
            $select = $this->select();

            if ($where !== null) {
                $this->_where($select, $where);
            }

            if ($order !== null) {
                $this->_order($select, $order);
            }

            if ($count !== null || $offset !== null) {
                $select->limit($count, $offset);
            }

        } else {
            $select = $where;
        }

        $rows = $this->_fetch($select);

        $data  = array(
            'table'    => $this,
            'data'     => $rows,
            'rowClass' => $this->_rowClass,
            'stored'   => true
        );

        $rowsetClass = $this->_rowsetClass;
        return new $rowsetClass($data);
    }

    /**
     * Fetches one row in an object of type Kwf_Db_Table_Row,
     * or returns null if no row matches the specified criteria.
     *
     * @param string|array|Kwf_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Kwf_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $offset OPTIONAL An SQL OFFSET value.
     * @return Kwf_Db_Table_Row|null The row results per the
     *     Zend_Db_Adapter fetch mode, or null if no row found.
     */
    public function fetchRow($where = null, $order = null, $offset = null)
    {
        if (!($where instanceof Kwf_Db_Table_Select)) {
            $select = $this->select();

            if ($where !== null) {
                $this->_where($select, $where);
            }

            if ($order !== null) {
                $this->_order($select, $order);
            }

            $select->limit(1, ((is_numeric($offset)) ? (int) $offset : null));

        } else {
            $select = $where->limit(1, $where->getPart(Zend_Db_Select::LIMIT_OFFSET));
        }

        $rows = $this->_fetch($select);

        if (count($rows) == 0) {
            return null;
        }

        $data = array(
            'table'   => $this,
            'data'     => $rows[0],
            'stored'  => true
        );

        $rowClass = $this->_rowClass;
        return new $rowClass($data);
    }

    /**
     * Fetches a new blank row (not from the database).
     *
     * @param  array $data OPTIONAL data to populate in the new row.
     * @return Kwf_Db_Table_Row_Abstract
     */
    public function createRow(array $data = array())
    {
        $cols     = $this->_getCols();
        $defaults = array_combine($cols, array_fill(0, count($cols), null));

        $config = array(
            'table'    => $this,
            'data'     => $defaults,
            'stored'   => false
        );

        $rowClass = $this->_rowClass;
        $row = new $rowClass($config);
        $row->setFromArray($data);
        return $row;
    }

    /**
     * Generate WHERE clause from user-supplied string or array
     *
     * @param  string|array $where  OPTIONAL An SQL WHERE clause.
     * @return Kwf_Db_Table_Select
     */
    protected function _where(Kwf_Db_Table_Select $select, $where)
    {
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

        return $select;
    }

    /**
     * Generate ORDER clause from user-supplied string or array
     *
     * @param  string|array $order  OPTIONAL An SQL ORDER clause.
     * @return Kwf_Db_Table_Select
     */
    protected function _order(Kwf_Db_Table_Select $select, $order)
    {
        if (!is_array($order)) {
            $order = array($order);
        }

        foreach ($order as $val) {
            $select->order($val);
        }

        return $select;
    }
}
