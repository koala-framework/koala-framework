<?php
class Vps_Model_Db implements Vps_Model_Interface
{
    protected $_rowClass = 'Vps_Model_Db_Row';
    protected $_rowsetClass = 'Vps_Model_Db_Rowset';
    protected $_table;
    protected $_tableName;
    protected $_default = array();

    public function __construct($config = array())
    {
        if (isset($config['table'])) {
            if (!is_object($config['table'])) {
                throw new Vps_Exception("Construct-config value 'table' needs to be an object for 'Vps_Model_Db'");
            }
            $this->_table = $config['table'];
        }
        if (isset($config['default'])) $this->_default = $config['default'];
        $this->_init();
    }

    protected function _init()
    {
        if (!isset($this->_table) && isset($this->_tableName)) {
            $this->_table = new $this->_tableName;
        }
    }

    public function getColumns()
    {
        return $this->_table->info(Zend_Db_Table_Abstract::COLS);
    }

    public function createRow(array $data=array())
    {
        $data = array_merge($this->_default, $data);
        return new $this->_rowClass(array(
            'row' => $this->_table->createRow($data),
            'model' => $this
        ));
    }

    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->find($id),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function createDbSelect($select)
    {
        if (!$select) return null;
        $tablename = $this->_table->info('name');
        $dbSelect = $this->_table->select();
        $dbSelect->from($tablename);
        if ($whereEquals = $select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            foreach ($whereEquals as $field=>$value) {
                if (is_array($value)) {
                    foreach ($value as &$v) {
                        $v = $this->getAdapter()->quote($v);
                    }
                    $value = implode(', ', $value);
                    $dbSelect->where("$tablename.$field IN ($value)");
                } else {
                    $dbSelect->where("$tablename.$field = ?", $value);
                }
            }
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE)) {
            foreach ($where as $w) {
                $dbSelect->where($w[0], $w[1], $w[2]);
            }
        }

        if ($whereId = $select->getPart(Vps_Model_Select::WHERE_ID)) {
            $dbSelect->where($tablename.'.'.$this->getPrimaryKey()." = ?", $whereId);
        }

        if ($whereNull = $select->getPart(Vps_Model_Select::WHERE_NULL)) {
            foreach ($whereNull as $field) {
                $dbSelect->where("ISNULL($tablename.$field)");
            }
        }

        if ($other = $select->getPart(Vps_Model_Select::OTHER)) {
            foreach ($other as $i) {
                call_user_func_array(array($dbSelect, $i['method']), $i['arguments']);
            }
        }
        return $dbSelect;
    }

    //Nur zum Debuggen verwenden!
    public function getSqlForSelect($select)
    {
        //TODO: limit und order fehlen :D
        $dbSelect = $this->createDbSelect($select);
        return $dbSelect->__toString();
    }

    public function fetchAll($where = array(), $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            $select = $this->select($where);
            if ($order) $select->order($order);
            if ($limit || $start) $select->limit($limit, $start);
        } else {
            $select = $where;
        }
        $dbSelect = $this->createDbSelect($select);
        if ($order = $select->getPart(Vps_Model_Select::ORDER)) {
            $tablename = $this->_table->info('name');
            foreach ($order as $o) {
                if ($o['field'] == Vps_Model_Select::ORDER_RAND) {
                    $dbSelect->order('RAND()');
                } else {
                    if (strpos($o['field'], '.')===false) {
                        $o['field'] = $tablename.'.'.$o['field'];
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
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->fetchAll($dbSelect),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function fetchCount($select = array())
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

    public function isEqual(Vps_Model_Interface $other) {
        if ($other instanceof Vps_Model_Db &&
            $this->getTable()->info(Zend_Db_Table_Abstract::NAME) ==
            $other->getTable()->info(Zend_Db_Table_Abstract::NAME)
        ) {
            return true;
        }
        return false;
    }

    public function select($where = array())
    {
        return new Vps_Model_Select($where);
    }
    
    public function getRowsetClass()
    {
        return $this->_rowsetClass;
    }
}
