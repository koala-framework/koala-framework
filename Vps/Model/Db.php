<?php
class Vps_Model_Db extends Vps_Model_Abstract
    implements Vps_Model_Interface_Id
{
    protected $_rowClass = 'Vps_Model_Db_Row';
    protected $_rowsetClass = 'Vps_Model_Db_Rowset';
    protected $_table;
    private $_tableName;
    private $_columns;

    private $_indirectSiblingModels = array();

    private $_importBuffer;
    private $_importBufferOptions;

    public function __construct($config = array())
    {
        if (isset($config['tableName'])) {
            $this->_table = new $config['tableName']();
        }
        if (isset($config['table'])) {
            $this->_table = $config['table'];
        }
        parent::__construct($config);
    }

    public function __destruct()
    {
        if (isset($this->_importBuffer)) {
            $this->writeBuffer();
        }
    }

    //kann gesetzt werden von proxy
    public function addIndirectSiblingModels($m)
    {
        $this->_indirectSiblingModels = array_merge($this->_indirectSiblingModels, $m);
    }
    protected function _init()
    {
        parent::_init();
        if (is_string($this->_table)) {
            $this->_tableName = $this->_table;
            $this->_table = new Vps_Db_Table(array(
                'name' => $this->_table
            ));
        }
        if (!$this->_table) {
            if (isset($this->_name)) {
                throw new Vps_Exception("You must rename _name to _table in '".get_class($this)."'");
            }
            throw new Vps_Exception("No table set");
        }
        if (!$this->_table instanceof Zend_Db_Table_Abstract) {
            throw new Vps_Exception("'".get_class($this->_table)."' is not a Zend_Db_Table");
        }
    }

    public function getOwnColumns()
    {
        if (!$this->_columns)
            $this->_columns = $this->_table->info(Zend_Db_Table_Abstract::COLS);
        return $this->_columns;
    }

    public function createRow(array $data=array())
    {
        $ret = new $this->_rowClass(array(
            'row' => $this->_table->createRow(),
            'model' => $this
        ));
        $data = array_merge($this->_default, $data);
        foreach ($data as $k=>$i) {
            $ret->$k = $i;
        }
        return $ret;
    }

    public function afterInsert($row)
    {
        $id = $this->_getUniqueId($row);
        $this->_rows[$id] = $row;
    }

    protected function _getUniqueId($row)
    {
        $keys = $this->getPrimaryKey();
        if (!is_array($keys)) $keys = array($keys);
        $ids = array();
        foreach ($keys as $key) {
            $key = $this->transformColumnName($key);
            $ids[] = $row->$key;
        }
        return implode('_', $ids);
    }

    public function getRowByProxiedRow($proxiedRow)
    {
        $id = $this->_getUniqueId($proxiedRow);
        if (!isset($this->_rows[$id])) {
            $this->_rows[$id] = new $this->_rowClass(array(
                'row' => $proxiedRow,
                'model' => $this
            ));
        }
        return $this->_rows[$id];
    }

    private function _formatField($field, $select)
    {
        if (in_array($field, $this->getOwnColumns())) {
            $f = $this->transformColumnName($field);
            return $this->getTableName().'.'.$f;
        }
        $ret = $this->_formatFieldInternal($field, $select);
        if (!$ret) {
            throw new Vps_Exception("Can't find field '$field'");
        }

        return $ret;
    }
    private function _formatFieldInternal($field, $dbSelect)
    {
        $sm = array_merge($this->_siblingModels, $this->_indirectSiblingModels);
        foreach ($sm as $k=>$m) {
            if (is_array($m)) {
                $siblingOf = $m['siblingOf'];
                $m = $m['sibling'];
            } else {
                $siblingOf = $this;
            }
            while ($m instanceof Vps_Model_Proxy) {
                $m = $m->getProxyModel();
            }
            if ($m instanceof Vps_Model_Db) {
                if (in_array($field, $m->getOwnColumns())) {
                    $ref = $m->getReferenceByModelClass(get_class($siblingOf), $k);
                    $siblingTableName = $m->getTableName();
                    $dbSelect->joinLeft($siblingTableName,
                            $this->getTableName().'.'.$this->getPrimaryKey()
                            .' = '.$siblingTableName.'.'.$ref['column'], array());
                    return $m->getTableName().'.'.$field;
                }
                $ret = $m->_formatFieldInternal($field, $dbSelect);
                if ($ret) return $ret;
            }
        }
        return false;
    }

    /**
     * Workaround
     *
     * @see http://framework.zend.com/issues/browse/ZF-1343
     * @see http://bugs.php.net/bug.php?id=44251
     */
    private function _fixStupidQuoteBug($v)
    {
        if ((strpos($v, '?') !== false || strpos($v, ':') !== false) && strpos($v, '\'') !== false) {
            $e = new Vps_Exception("(? or :) and ' are used together in an sql query value. This is a problem because of an Php bug. ' is ignored.");
            $e->notify();
            $v = str_replace('\'', '', $v);
        }
        return $v;
    }

    public function createDbSelect($select)
    {
        if (!$select) return null;
        $tablename = $this->getTableName();
        $dbSelect = $this->_table->select();
        $dbSelect->from($tablename);
        $this->_applySelect($dbSelect, $select);
        return $dbSelect;
    }

    private function _applySelect($dbSelect, $select)
    {
        if ($whereEquals = $select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            foreach ($whereEquals as $field=>$value) {
                if (is_array($value)) {
                    foreach ($value as &$v) {
                        $v = $this->_fixStupidQuoteBug($v);
                        $v = $this->getAdapter()->quote($v);
                    }
                    $value = implode(', ', $value);
                    $dbSelect->where($this->_formatField($field, $dbSelect)." IN ($value)");
                } else {
                    $value = $this->_fixStupidQuoteBug($value);
                    $dbSelect->where($this->_formatField($field, $dbSelect)." = ?", $value);
                }
            }
        }
        if ($whereNotEquals = $select->getPart(Vps_Model_Select::WHERE_NOT_EQUALS)) {
            foreach ($whereNotEquals as $field=>$value) {
                if (is_array($value)) {
                    foreach ($value as &$v) {
                        $v = $this->_fixStupidQuoteBug($v);
                        $v = $this->getAdapter()->quote($v);
                    }
                    $value = implode(', ', $value);
                    $dbSelect->where($this->_formatField($field, $dbSelect)." NOT IN ($value)");
                } else {
                    $value = $this->_fixStupidQuoteBug($value);
                    $dbSelect->where($this->_formatField($field, $dbSelect)." != ?", $value);
                }
            }
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE)) {
            foreach ($where as $w) {
                $dbSelect->where($w[0], $w[1], $w[2]);
            }
        }

        if ($whereId = $select->getPart(Vps_Model_Select::WHERE_ID)) {
            $whereId = $this->_fixStupidQuoteBug($whereId);
            $dbSelect->where($this->_formatField($this->getPrimaryKey(), $dbSelect)." = ?", $whereId);
        }

        if ($whereNull = $select->getPart(Vps_Model_Select::WHERE_NULL)) {
            foreach ($whereNull as $field) {
                $dbSelect->where("ISNULL(".$this->_formatField($field, $dbSelect).")");
            }
        }

        if ($other = $select->getPart(Vps_Model_Select::OTHER)) {
            foreach ($other as $i) {
                call_user_func_array(array($dbSelect, $i['method']), $i['arguments']);
            }
        }
        if ($whereExpression = $select->getPart(Vps_Model_Select::WHERE_EXPRESSION)) {
            foreach ($whereExpression as $expr) {
                $expr->validate();
                $dbSelect->where($this->_createDbSelectExpression($expr, $dbSelect));
            }
        }
    }

    private function _createDbSelectExpression($expr, $dbSelect)
    {
        if ($expr instanceof Vps_Model_Select_Expr_CompareField_Abstract) {
            $quotedValue = $expr->getValue();
            $quotedValue = $this->_fixStupidQuoteBug($quotedValue);
            $quotedValue = $this->_table->getAdapter()->quote($quotedValue);
        }
        if ($expr instanceof Vps_Model_Select_Expr_CompareField_Abstract ||
            $expr instanceof Vps_Model_Select_Expr_IsNull
        ) {
            $field = $this->_formatField($expr->getField(), $dbSelect);
        }
        if ($expr instanceof Vps_Model_Select_Expr_Equals) {
            return $field." = ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_IsNull) {
            return $field." IS NULL";
        } else if ($expr instanceof Vps_Model_Select_Expr_Smaller
                || $expr instanceof Vps_Model_Select_Expr_SmallerDate) {
            return $field." < ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_Higher
                || $expr instanceof Vps_Model_Select_Expr_HigherDate) {
            return $field." > ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_Like) {
            $quotedValue = str_replace("_", "\\_", $quotedValue);
            if ($expr instanceof Vps_Model_Select_Expr_Contains) {
                $v = $expr->getValue();
                $v = $this->_fixStupidQuoteBug($v);
                $quotedValueContains = $this->_table->getAdapter()->quote('%'.$v.'%');

                $quotedValue = str_replace("%", "\\%", $quotedValue);
                $quotedValue = str_replace(
                                substr($quotedValueContains, 2, strlen($quotedValueContains)-4),
                                substr($quotedValue, 1, strlen($quotedValue)-2),
                                $quotedValueContains);
            }
            return $field." LIKE ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_StartsWith) {
            return "LEFT($field, ".strlen($this->_fixStupidQuoteBug($expr->getValue())).") = ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_NOT) {
            return "NOT (".$this->_createDbSelectExpression($expr->getExpression(), $dbSelect).")";
        } else if ($expr instanceof Vps_Model_Select_Expr_Or) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect).")";
            }
            return implode(" OR ", $sqlExpressions);
        } else if ($expr instanceof Vps_Model_Select_Expr_And) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression, $dbSelect).")";
            }
            return implode(" AND ", $sqlExpressions);
        }
    }

    //Nur zum Debuggen verwenden!
    public function getSqlForSelect($select)
    {
        //TODO: limit und order fehlen :D
        $dbSelect = $this->createDbSelect($select);
        return $dbSelect->__toString();
    }

    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->find($id),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function getIds($where = array(), $order=null, $limit=null, $start=null)
    {
        $dbSelect = $this->_getDbSelect($where, $order, $limit, $start);
        $id = $this->getPrimaryKey();
        $ret = array();
        foreach ($this->_table->fetchAll($dbSelect) as $row) {
            $ret[] = $row->$id;
        }
        return $ret;
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        $dbSelect = $this->_getDbSelect($where, $order, $limit, $start);
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->fetchAll($dbSelect),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function deleteRows($where)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where);
        } else {
            $select = $where;
        }
        if ($select->getPart(Vps_Model_Select::OTHER) ||
            $select->getPart(Vps_Model_Select::LIMIT_COUNT) ||
            $select->getPart(Vps_Model_Select::LIMIT_OFFSET))
            throw new Vps_Exception('Select for delete must only contain where* parts');
        $dbSelect = new Zend_Db_Select($this->getAdapter());
        $this->_applySelect($dbSelect, $select);
        $where = array();
        foreach ($dbSelect->getPart('where') as $part) {
            if (substr($part, 0, 4) == 'AND ') $part = substr($part, 4);
            $where[] = $part;
        }
        return $this->_table->delete($where);
    }

    private function _getDbSelect($where, $order, $limit, $start)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $dbSelect = $this->createDbSelect($select);
        if ($order = $select->getPart(Vps_Model_Select::ORDER)) {
            foreach ($order as $o) {
                if ($o['field'] instanceof Zend_Db_Expr) {
                    $dbSelect->order($o['field']);
                } else if ($o['field'] == Vps_Model_Select::ORDER_RAND) {
                    $dbSelect->order('RAND()');
                } else {
                    if (strpos($o['field'], '.') === false &&
                        strpos($o['field'], '(') === false
                    ) {
                        $o['field'] = $this->_formatField($o['field'], $dbSelect);
                    }
                    $dbSelect->order($o['field'].' '.$o['direction']);
                }
            }
        }
        $limitCount = $select->getPart(Vps_Model_Select::LIMIT_COUNT);
        $limitOffset = $select->getPart(Vps_Model_Select::LIMIT_OFFSET);
        if ($limitCount || $limitOffset) {
            $dbSelect->limit($limitCount, $limitOffset);
        }
        return $dbSelect;
    }

    public function countRows($select = array())
    {

        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $dbSelect = $this->createDbSelect($select);
        $dbSelect->reset(Zend_Db_Select::COLUMNS);
        $dbSelect->setIntegrityCheck(false);
        if ($dbSelect->getPart('group')) {
            $group = current($dbSelect->getPart('group'));
            $dbSelect->reset(Zend_Db_Select::GROUP);
            $dbSelect->from(null, "COUNT(DISTINCT $group) c");
        } else {
            $dbSelect->from(null, 'COUNT(*) c');
        }
        return $this->_table->getAdapter()->query($dbSelect)->fetchColumn();
    }

    public function getPrimaryKey()
    {
        $ret = $this->_table->info('primary');
        if (sizeof($ret) == 1) {
            $ret = array_values($ret);
            $ret = $ret[0];
        }
        return $ret;
    }

    public function getTable()
    {
        return $this->_table;
    }

    public function getAdapter()
    {
        return $this->getTable()->getAdapter();
    }

    public function getTableName()
    {
        if (!$this->_tableName)
            $this->_tableName = $this->_table->info(Zend_Db_Table_Abstract::NAME);
        return $this->_tableName;
    }

    public function isEqual(Vps_Model_Interface $other) {
        if ($other instanceof Vps_Model_Db &&
            $this->getTableName() == $other->getTableName()) {
            return true;
        }
        return false;
    }

    public function select($where = array(), $order = null, $limit = null, $start = null)
    {
        if (!is_array($where)) {
            $ret = new Vps_Model_Select();
            if ($where) {
                $ret->whereEquals($this->getPrimaryKey(), $where);
            }
        } else {
            $ret = new Vps_Model_Select($where);
        }
        if ($order) $ret->order($order);
        if ($limit || $start) $ret->limit($limit, $start);
        return $ret;
    }

    public function getUniqueIdentifier()
    {
        return $this->getTableName();
    }


    public function export($format, $select = array())
    {
        if ($format == self::FORMAT_SQL) {
            if ($select) {
                throw new Vps_Exception("Select object may not be used when using SQL-Export");
            }
            $systemData = $this->_getSystemData();
            $filename = tempnam('/tmp', 'modelimport');
            $cmd = "{$systemData['mysqlDir']}mysqldump --add-drop-table=false --no-create-info=true "
                ."$systemData[mysqlOptions] $systemData[tableName] | sed -e \"s|INSERT INTO|REPLACE INTO|\" | gzip -c > $filename";
            exec($cmd, $output, $ret);
            if ($ret != 0) throw new Vps_Exception("SQL export failed");
            $ret = file_get_contents($filename);
            unlink($filename);
            return $ret;
        } else if ($format == self::FORMAT_ARRAY) {
            if (!is_object($select)) {
                if (is_string($select)) $where = array($select);
                $select = $this->select($select);
            }
            $dbSelect = $this->createDbSelect($select);
            if (!$dbSelect) return array();
            return $dbSelect->query()->fetchAll();
        } else {
            return parent::export($format, $select);
        }
    }

    public function import($format, $data, $options = array())
    {
        if ($format == self::FORMAT_SQL) {
            if ($options) {
                throw new Vps_Exception_NotYetImplemented();
            }
            $filename = tempnam('/tmp', 'modelimport');
            file_put_contents($filename, $data);

            $systemData = $this->_getSystemData();
            $cmd = "gunzip -c $filename | {$systemData['mysqlDir']}mysql $systemData[mysqlOptions] 2>&1";
            exec($cmd, $output, $ret);
            if ($ret != 0) throw new Vps_Exception("SQL import failed: ".implode("\n", $output));
            unlink($filename);
        } else if ($format == self::FORMAT_ARRAY) {
            if (isset($options['buffer']) && $options['buffer']) {
                if (isset($this->_importBuffer)) {
                    if ($options != $this->_importBufferOptions) {
                        throw new Vps_Exception_NotYetImplemented("You can't buffer imports with different options (not yet implemented)");
                    }
                    $this->_importBuffer = array_merge($this->_importBuffer, $data);
                    if (isset($options['bufferSize']) &&
                        count($this->_importBuffer) > $options['bufferSize'])
                    {
                        $this->writeBuffer();
                    }
                } else {
                    $this->_importBufferOptions = $options;
                    $this->_importBuffer = $data;
                }
            } else {
                $this->_importArray($data, $options);
            }
        } else {
            parent::import($format, $data);
        }
    }

    private function _getSystemData()
    {
        $ret = array();

        $dbConfig = Zend_Registry::get('db')->getConfig();
        $ret['mysqlOptions'] = "--host={$dbConfig['host']} --user={$dbConfig['username']} --password={$dbConfig['password']} {$dbConfig['dbname']} ";
        $config = Zend_Registry::get('config');

        $ret['mysqlDir'] = '';
        if ($config->server->host == 'vivid-planet.com') {
            $ret['mysqlDir'] = '/usr/local/mysql/bin/';
        }
        $ret['tableName'] = $this->getTableName();

        return $ret;
    }

    public function writeBuffer()
    {
        parent::writeBuffer();
        if (isset($this->_importBuffer)) {
            $this->_importArray($this->_importBuffer, $this->_importBufferOptions);
            unset($this->_importBuffer);
            unset($this->_importBufferOptions);
        }
    }

    private function _importArray($data, $options)
    {
        if (empty($data)) return;
        $fields = array_keys($data[0]);
        if (isset($options['replace']) && $options['replace']) {
            $sql = 'REPLACE';
        } else {
            $sql = 'INSERT';
        }
        $sql .= ' INTO '.$this->getTableName().' ('.implode(', ', $fields).') VALUES ';
        foreach ($data as $d) {
            if (array_keys($d) != $fields) {
                throw new Vps_Exception_NotYetImplemented("You must have always the same keys when importing");
            }
            $sql .= '(';
            foreach ($d as $i) {
                if (is_null($i)) {
                    $sql .= 'NULL';
                } else {
                    $sql .= $this->_table->getAdapter()->quote($i);
                }
                $sql .= ',';
            }
            $sql = substr($sql, 0, -1);
            $sql .= '),';
        }
        $sql = substr($sql, 0, -1);
        $this->executeSql($sql);
    }

    public function executeSql($sql)
    {
        // Performance, bei Pdo wird der Adapter umgangen
        if ($this->_table->getAdapter() instanceof Zend_Db_Adapter_Pdo_Mysql) {
            $this->_table->getAdapter()->getConnection()->exec($sql);
        } else {
            $this->_table->getAdapter()->query($sql);
        }
    }
}
