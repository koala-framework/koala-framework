<?php
/**
 * @internal
 */
class Kwf_Db_Table_Select extends Zend_Db_Select
{
    public function where($cond, $value = null, $type = null)
    {
        if (is_array($cond)) {
            foreach ($cond as $key => $val) {
                // is $key an int?
                if (is_int($key)) {
                    // $val is the full condition
                    $this->where($val);
                } else {
                    // $key is the condition with placeholder,
                    // and $val is quoted into the condition
                    $this->where($key, $val);
                }
            }
            return $this;
        } else {
            return parent::where($cond, $value, $type);
        }
    }
    public function limit($count = null, $offset = null)
    {
        if (is_array($count)) {
            $offset = $count['start'];
            $count = $count['limit'];
        }
        return parent::limit($count, $offset);
    }

    public function getTableName()
    {
        return $this->_info['name'];
    }

    public function info()
    {
        return $this->_info;
    }

    /**
     * Adds the needed wheres for a search
     *
     * @deprecated use Kwf_Model_Select_Expr_SearchLike instead
     * @param string|array $searchValues An array with the db-field in the key
     * and the search value as value. Field query means to search in all given fields
     * for this value (see the second argument). If a string is given, it is interpreted
     * like array('query' => $searchValues).
     * @param string|array $searchFields The fields that should be searched with
     * search field 'query'. If a string is given, it is treated like array($string).
     * Value '*' means to search in all fields in the table.
     * @return object $this The select object itself
     */
    public function searchLike($searchValues = array(), $searchFields = '*')
    {
        if (is_string($searchValues)) $searchValues = array('query' => $searchValues);
        if (is_string($searchFields)) $searchFields = array($searchFields);

        $selectInfo = $this->info();
        foreach ($searchValues as $column => $value) {
            if (empty($value) ||
                ($column != 'query' && !in_array($column, $selectInfo['cols']))
            ) {
                continue;
            }
            $searchWords = preg_split('/[\s-+,;*]/', $value);
            foreach ($searchWords as $searchWord) {
                $searchWord = trim($searchWord);
                if (empty($searchWord)) continue;

                if ($column == 'query') {
                    if (!$searchFields) {
                        throw new Kwf_Exception("Search field 'query' was found, "
                        ."but no 'searchFields' were given as second argument in "
                        ."'searchLike' method of Kwf_Db_Table_Select object");
                    }
                    if (in_array('*', $searchFields)) {
                        $searchFields = array_merge($searchFields, $selectInfo['cols']);
                        foreach ($searchFields as $sfk => $sfv) {
                            if ($sfv == '*') unset($searchFields[$sfk]);
                            if (substr($sfv, 0, 1) == '!') {
                                unset($searchFields[$sfk]);
                                unset($searchFields[array_search(substr($sfv, 1), $searchFields)]);
                            }
                        }
                    }

                    $wheres = array();
                    foreach ($searchFields as $field) {
                        if (strpos($field, '.') === false) {
                            $field = $selectInfo['name'].'.'.$field;
                        }
                        $wheres[] = Kwf_Registry::get('db')->quoteInto(
                            $field.' LIKE ?', "%$searchWord%"
                        );
                    }
                    $this->where(implode(' OR ', $wheres));
                } else {
                    $this->where($column.' LIKE ?', "%".$searchWord."%");
                }
            }
        }
        return $this;
    }

    public function assembleIntoOutfile($outFile)
    {
        $sql = self::SQL_SELECT;
        foreach (array_keys(self::$_partsInit) as $part) {
            if ($part == self::FROM) {
                $sql .= " INTO OUTFILE '$outFile'";
                $sql .= " FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' ESCAPED BY '\\\\' LINES TERMINATED BY '\\n'";
            }
            $method = '_render' . ucfirst($part);
            if (method_exists($this, $method)) {
                $sql = $this->$method($sql);
            }
        }
        return $sql;
    }

    public function setPart($part, $value)
    {
        $part = strtolower($part);
        $this->_parts[$part] = $value;
    }








    /**
     * Table schema for parent Kwf_Db_Table.
     *
     * @var array
     */
    protected $_info;

    /**
     * Table integrity override.
     *
     * @var array
     */
    protected $_integrityCheck = true;

    /**
     * Table instance that created this select object
     *
     * @var Kwf_Db_Table
     */
    protected $_table;

    /**
     * Class constructor
     *
     * @param Kwf_Db_Table $table
     */
    public function __construct(Kwf_Db_Table $table)
    {
        parent::__construct($table->getAdapter());

        $this->setTable($table);
    }

    /**
     * Return the table that created this select object
     *
     * @return Kwf_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Sets the primary table name and retrieves the table schema.
     *
     * @param Kwf_Db_Table $table
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function setTable(Kwf_Db_Table $table)
    {
        $this->_adapter = $table->getAdapter();
        $this->_info    = $table->info();
        $this->_table   = $table;

        return $this;
    }

    /**
     * Sets the integrity check flag.
     *
     * Setting this flag to false skips the checks for table joins, allowing
     * 'hybrid' table rows to be created.
     *
     * @param Kwf_Db_Table $adapter
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function setIntegrityCheck($flag = true)
    {
        $this->_integrityCheck = $flag;
        return $this;
    }

    /**
     * Tests query to determine if expressions or aliases columns exist.
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        $readOnly = false;
        $fields   = $this->getPart(Kwf_Db_Table_Select::COLUMNS);
        $cols     = $this->_info[Kwf_Db_Table::COLS];

        if (!count($fields)) {
            return $readOnly;
        }

        foreach ($fields as $columnEntry) {
            $column = $columnEntry[1];
            $alias = $columnEntry[2];

            if ($alias !== null) {
                $column = $alias;
            }

            switch (true) {
                case ($column == self::SQL_WILDCARD):
                    break;

                case ($column instanceof Zend_Db_Expr):
                case (!in_array($column, $cols)):
                    $readOnly = true;
                    break 2;
            }
        }

        return $readOnly;
    }

    /**
     * Adds a FROM table and optional columns to the query.
     *
     * The table name can be expressed
     *
     * @param  array|string|Zend_Db_Expr|Kwf_Db_Table $name The table name or an
                                                                      associative array relating
                                                                      table name to correlation
                                                                      name.
     * @param  array|string|Zend_Db_Expr $cols The columns to select from this table.
     * @param  string $schema The schema name to specify, if any.
     * @return Kwf_Db_Table_Select This Kwf_Db_Table_Select object.
     */
    public function from($name, $cols = self::SQL_WILDCARD, $schema = null)
    {
        if ($name instanceof Kwf_Db_Table) {
            $info = $name->info();
            $name = $info[Kwf_Db_Table::NAME];
            if (isset($info[Kwf_Db_Table::SCHEMA])) {
                $schema = $info[Kwf_Db_Table::SCHEMA];
            }
        }

        return $this->joinInner($name, null, $cols, $schema);
    }

    /**
     * Performs a validation on the select query before passing back to the parent class.
     * Ensures that only columns from the primary Kwf_Db_Table are returned in the result.
     *
     * @return string|null This object as a SELECT string (or null if a string cannot be produced)
     */
    public function assemble()
    {
        $fields  = $this->getPart(Kwf_Db_Table_Select::COLUMNS);
        $primary = $this->_info[Kwf_Db_Table::NAME];
        $schema  = $this->_info[Kwf_Db_Table::SCHEMA];


        if (count($this->_parts[self::UNION]) == 0) {

            // If no fields are specified we assume all fields from primary table
            if (!count($fields)) {
                $this->from($primary, self::SQL_WILDCARD, $schema);
                $fields = $this->getPart(Kwf_Db_Table_Select::COLUMNS);
            }

            $from = $this->getPart(Kwf_Db_Table_Select::FROM);

            if ($this->_integrityCheck !== false) {
                foreach ($fields as $columnEntry) {
                    list($table, $column) = $columnEntry;

                    // Check each column to ensure it only references the primary table
                    if ($column) {
                        if (!isset($from[$table]) || $from[$table]['tableName'] != $primary) {
                            throw new Kwf_Exception('Select query cannot join with another table');
                        }
                    }
                }
            }
        }

        return parent::assemble();
    }

    public function __toString()
    {
        try {
            return parent::__toString();
        } catch (Exception $e) {
            echo $e;
            exit;
        }
    }
}
