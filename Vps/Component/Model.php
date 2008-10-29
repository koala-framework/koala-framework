<?php
class Vps_Component_Model extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Component_Model_Row';
    protected $_rowsetClass = 'Vps_Component_Model_Rowset';
    protected $_constraints = array(
        'pageGenerator' => true,
        'ignoreVisible' => true
    );

    public function createRow(array $data=array()) {
        throw new Vps_Exception('Not implemented yet.');
    }

    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'dataKeys' => array(Vps_Component_Data_Root::getInstance()->getComponentById($id, $this->_constraints)),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function isEqual(Vps_Model_Interface $other) {
        if ($other instanceof Vps_Component_Model &&
            $this->getTable()->info(Zend_Db_Table_Abstract::NAME) ==
            $other->getTable()->info(Zend_Db_Table_Abstract::NAME)
        ) {
            return true;
        }
        return false;
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            $select = $this->select();
            if ($where) $select->where($where);
            if ($order) $select->order($order);
            if ($limit || $start) $select->limit($limit, $start);
        } else {
            $select = $where;
        }

        $root = Vps_Component_Data_Root::getInstance();

        $where = $select->getPart(Vps_Model_Select::WHERE_EQUALS);
        if ($where && isset($where['parent_id'])) {
            $constraints = $this->_constraints;
            if ($where['parent_id'] == 'root') {
                $rowset = array();
                foreach (Zend_Registry::get('config')->vpc->pageTypes->toArray() as $id => $name) {
                    $id = 'root-' . $id;
                    $rowset[] = new Vps_Component_Data_Category($id, $name);
                }
            } else {
                if (substr($where['parent_id'], 0, 5) == 'root-') {
                    $constraints['type'] = substr($where['parent_id'], 5);
                    $where['parent_id'] = 'root';
                }
                $page = $root->getComponentById($where['parent_id'], $this->_constraints);
                $rowset = array_values($page->getChildComponents($constraints));
            }
        } else {
            $rowset = array($root);
        }
        return new $this->_rowsetClass(array(
            'dataKeys' => $rowset,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function getRowByDataKey($component)
    {
        $key = $component->componentId;
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'data' => $component,
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

    public function fetchCount($where = array())
    {
        throw new Vps_Exception('Not implemented yet.');
    }

    public function getPrimaryKey()
    {
        return 'componentId';
    }

    public function getTable()
    {
        return new Vps_Dao_Pages();
    }

    public function getColumns()
    {
        throw new Vps_Exception('Not implemented yet.');
    }

    public function getUniqueIdentifier()
    {
        throw new Vps_Exception("no unique identifier set");
    }
}