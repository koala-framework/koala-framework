<?php
class Vps_Model_Db extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Db_Row';
    protected $_rowsetClass = 'Vps_Model_Db_Rowset';
    protected $_table;

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

    protected function _init()
    {
        parent::_init();
        if (is_string($this->_table)) {
            $this->_table = new Vps_Db_Table(array(
                'name' => $this->_table
            ));
        }
        if (!$this->_table instanceof Zend_Db_Table_Abstract) {
            throw new Vps_Exception("'".get_class($this->_table)."' is not a Zend_Db_Table");
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
        if ($whereNotEquals = $select->getPart(Vps_Model_Select::WHERE_NOT_EQUALS)) {
            foreach ($whereNotEquals as $field=>$value) {
                if (is_array($value)) {
                    foreach ($value as &$v) {
                        $v = $this->getAdapter()->quote($v);
                    }
                    $value = implode(', ', $value);
                    $dbSelect->where("$tablename.$field NOT IN ($value)");
                } else {
                    $dbSelect->where("$tablename.$field != ?", $value);
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

    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->find($id),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $dbSelect = $this->createDbSelect($select);
        if ($order = $select->getPart(Vps_Model_Select::ORDER)) {
            $tablename = $this->_table->info('name');
            foreach ($order as $o) {
                if ($o['field'] == Vps_Model_Select::ORDER_RAND) {
                    $dbSelect->order('RAND()');
                } else if ($o['field'] instanceof Zend_Db_Expr) {
                    $dbSelect->order($o['field']);
                } else {
                    if (strpos($o['field'], '.') === false &&
                        strpos($o['field'], '(') === false
                    ) {
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

    public function isEqual(Vps_Model_Interface $other) {
        if ($other instanceof Vps_Model_Db &&
            $this->getTable()->info(Zend_Db_Table_Abstract::NAME) ==
            $other->getTable()->info(Zend_Db_Table_Abstract::NAME)
        ) {
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
}
