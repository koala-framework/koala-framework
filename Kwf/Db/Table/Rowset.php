<?php
/**
 * @internal
 */
class Kwf_Db_Table_Rowset implements SeekableIterator, Countable, ArrayAccess
{
    /**
     * Rowset in Array wie es für Ext2.store.ArrayReader benötigt wird umwandeln.
     **/
    public function toStringDataArray($fields = array('id', '__toString'))
    {
        if (is_string($fields)) {
            //falls nur die id als string angegeben wurde
            $fields = array($field, '__toString');
        }
        $data = array();
        foreach ($this as $row) {
            $d = array();
            foreach ($fields as $f) {
                if ($f == '__toString') {
                    $d[] = $row->__toString();
                } else {
                    $d[] = $row->$f;
                }
            }
            $data[] = $d;
        }
        return $data;
    }
    /**
     * Sortieren nach jedem beliebigen Feld das die row zurückgibt.
     * auch solche die nur in row::__get existieren
     *
     * (recht langsam, erstellt alle rows)
     **/
    public function sort($order, $count = null, $offset = null)
    {
        if (!count($this)) return;

        $sortFields = explode(',', $order);
        $sortData = array();
        foreach ($this as $row) {
            foreach ($sortFields as $k=>$i) {
                $i = trim($i);
                if (substr($i, -4) == 'DESC') $i = substr($i, 0, -4);
                if (substr($i, -3) == 'ASC') $i = substr($i, 0, -3);
                $i = trim($i);
                if (!isset($row->$i)) {
                    throw new Kwf_Exception("Can't sort by '$i', field doesn't exist in row");
                }
                $sortData[$k][] = $row->$i;
            }
            $rows[] = $row;
        }
        $args = array();
        foreach ($sortFields as $k=>$i) {
            $i = trim($i);
            $args[] = $sortData[$k];
            if (substr($i, -4) == 'DESC') $args[] = SORT_DESC;
            if (substr($i, -3) == 'ASC') $args[] = SORT_ASC;
        }
        $args[] =& $rows; //ohne & hört sich der spaß auf

        if (!call_user_func_array('array_multisort', $args)) {
            throw new Kwf_Exception("Can't sort by '$order', array_multisort returned an error");
        }
        $this->_rows = array();

        //set the rows in the new order
        //does not update _data - so who cares?
        foreach ($rows as $i=>$row) {
            if ($offset && $i < $offset) continue;
            $this->_rows[$i] = $row;
            if ($count && $i >= count($this->_rows)) break;
        }
    }
    public function toDebug()
    {
        $i = get_class($this);
        $ret = print_r($this->_data, true);
        $ret = preg_replace('#^Array#', $i, $ret);
        $ret = "<pre>$ret</pre>";
        return $ret;
    }





































    /**
     * The original data for each row.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Kwf_Db_Table object.
     *
     * @var Kwf_Db_Table
     */
    protected $_table;

    /**
     * Connected is true if we have a reference to a live
     * Kwf_Db_Table object.
     * This is false after the Rowset has been deserialized.
     *
     * @var boolean
     */
    protected $_connected = true;

    /**
     * Kwf_Db_Table class name.
     *
     * @var string
     */
    protected $_tableClass;

    /**
     * Kwf_Db_Table_Row_Abstract class name.
     *
     * @var string
     */
    protected $_rowClass = 'Kwf_Db_Table_Row';

    /**
     * Iterator pointer.
     *
     * @var integer
     */
    protected $_pointer = 0;

    /**
     * How many data rows there are.
     *
     * @var integer
     */
    protected $_count;

    /**
     * Collection of instantiated Kwf_Db_Table_Row objects.
     *
     * @var array
     */
    protected $_rows = array();

    /**
     * @var boolean
     */
    protected $_stored = false;

    /**
     * @var boolean
     */
    protected $_readOnly = false;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['table'])) {
            $this->_table      = $config['table'];
            $this->_tableClass = get_class($this->_table);
        }
        if (isset($config['rowClass'])) {
            $this->_rowClass   = $config['rowClass'];
        }
        if (isset($config['data'])) {
            $this->_data       = $config['data'];
        }
        if (isset($config['readOnly'])) {
            $this->_readOnly   = $config['readOnly'];
        }
        if (isset($config['stored'])) {
            $this->_stored     = $config['stored'];
        }

        // set the count of rows
        $this->_count = count($this->_data);

        $this->init();
    }

    /**
     * Store data, class names, and state in serialized object
     *
     * @return array
     */
    public function __sleep()
    {
        return array('_data', '_tableClass', '_rowClass', '_pointer', '_count', '_rows', '_stored',
                     '_readOnly');
    }

    /**
     * Setup to do on wakeup.
     * A de-serialized Rowset should not be assumed to have access to a live
     * database connection, so set _connected = false.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->_connected = false;
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
     * Return the connected state of the rowset.
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->_connected;
    }

    /**
     * Returns the table object, or null if this is disconnected rowset
     *
     * @return Kwf_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Set the table object, to re-establish a live connection
     * to the database for a Rowset that has been de-serialized.
     *
     * @param Kwf_Db_Table $table
     * @return boolean
     * @throws Kwf_Exception
     */
    public function setTable(Kwf_Db_Table $table)
    {
        $this->_table = $table;
        $this->_connected = false;
        // @todo This works only if we have iterated through
        // the result set once to instantiate the rows.
        foreach ($this as $row) {
            $connected = $row->setTable($table);
            if ($connected == true) {
                $this->_connected = true;
            }
        }
        $this->rewind();
        return $this->_connected;
    }

    /**
     * Query the class name of the Table object for which this
     * Rowset was created.
     *
     * @return string
     */
    public function getTableClass()
    {
        return $this->_tableClass;
    }

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return Kwf_Db_Table_Rowset Fluent interface.
     */
    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return Kwf_Db_Table_Row_Abstract current element from the collection
     */
    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }

        // return the row object
        return $this->_loadAndReturnRow($this->_pointer);
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return int
     */
    public function key()
    {
        return $this->_pointer;
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return void
     */
    public function next()
    {
        ++$this->_pointer;
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * Required by interface Iterator.
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
        return $this->_pointer >= 0 && $this->_pointer < $this->_count;
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Implements Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Take the Iterator to position $position
     * Required by interface SeekableIterator.
     *
     * @param int $position the position to seek to
     * @return Kwf_Db_Table_Rowset
     * @throws Kwf_Exception
     */
    public function seek($position)
    {
        $position = (int) $position;
        if ($position < 0 || $position >= $this->_count) {
            throw new Kwf_Exception("Illegal index $position");
        }
        $this->_pointer = $position;
        return $this;
    }

    /**
     * Check if an offset exists
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[(int) $offset]);
    }

    /**
     * Get the row for the given offset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return Kwf_Db_Table_Row_Abstract
     */
    public function offsetGet($offset)
    {
        $offset = (int) $offset;
        if ($offset < 0 || $offset >= $this->_count) {
            throw new Kwf_Exception("Illegal index $offset");
        }
        $this->_pointer = $offset;

        return $this->current();
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Returns a Kwf_Db_Table_Row from a known position into the Iterator
     *
     * @param int $position the position of the row expected
     * @param bool $seek wether or not seek the iterator to that position after
     * @return Kwf_Db_Table_Row
     * @throws Kwf_Exception
     */
    public function getRow($position, $seek = false)
    {
        try {
            $row = $this->_loadAndReturnRow($position);
        } catch (Kwf_Exception $e) {
            throw new Kwf_Exception('No row could be found at position ' . (int) $position, 0, $e);
        }

        if ($seek == true) {
            $this->seek($position);
        }

        return $row;
    }

    /**
     * Returns all data as an array.
     *
     * Updates the $_data property with current row object values.
     *
     * @return array
     */
    public function toArray()
    {
        // @todo This works only if we have iterated through
        // the result set once to instantiate the rows.
        foreach ($this->_rows as $i => $row) {
            $this->_data[$i] = $row->toArray();
        }
        return $this->_data;
    }

    protected function _loadAndReturnRow($position)
    {
        if (!isset($this->_data[$position])) {
            throw new Kwf_Exception("Data for provided position does not exist");
        }

        // do we already have a row object for this position?
        if (empty($this->_rows[$position])) {
            $this->_rows[$position] = new $this->_rowClass(
                array(
                    'table'    => $this->_table,
                    'data'     => $this->_data[$position],
                    'stored'   => $this->_stored,
                    'readOnly' => $this->_readOnly
                )
            );

            if ( $this->_table instanceof Kwf_Db_Table ) {
                $info = $this->_table->info();

                if ( $this->_rows[$position] instanceof Kwf_Db_Table_Row_Abstract ) {
                    if ($info['cols'] == array_keys($this->_data[$position])) {
                        $this->_rows[$position]->setTable($this->getTable());
                    }
                }
            } else {
                $this->_rows[$position]->setTable(null);
            }
        }

        // return the row object
        return $this->_rows[$position];
    }
}
