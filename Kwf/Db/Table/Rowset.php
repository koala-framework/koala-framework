<?php
/**
 * @internal
 */
class Kwf_Db_Table_Rowset implements SeekableIterator, Countable, ArrayAccess
{
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
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['table'])) {
            $this->_table      = $config['table'];
        }
        if (isset($config['rowClass'])) {
            $this->_rowClass   = $config['rowClass'];
        }
        if (isset($config['data'])) {
            $this->_data       = $config['data'];
        }
        if (isset($config['stored'])) {
            $this->_stored     = $config['stored'];
        }

        // set the count of rows
        $this->_count = count($this->_data);

        $this->init();
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
     * Returns the table object, or null if this is disconnected rowset
     *
     * @return Kwf_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
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
                    'stored'   => $this->_stored
                )
            );
        }

        // return the row object
        return $this->_rows[$position];
    }
}
