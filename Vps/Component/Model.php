<?php
class Vps_Component_Model extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Component_Model_Row';
    protected $_primaryKey = 'componentId';
    private $_root;

    public function setRoot(Vps_Component_Data $root)
    {
        $this->_root = $root;
    }

    public function getRoot()
    {
        if (!$this->_root) $this->_root = Vps_Component_Data_Root::getInstance();
        return $this->_root;
    }

    public function createRow(array $data=array()) {
        throw new Vps_Exception('Not implemented yet.');
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

        $root = $this->getRoot();

        $where = $select->getPart(Vps_Model_Select::WHERE_EQUALS);
        $parts = $select->getPart(Vps_Model_Select::WHERE_NULL);

        if ($select->hasPart(Vps_Model_Select::WHERE_EXPRESSION)) { // Suchfeld
            $model = Vps_Model_Abstract::getInstance('Vpc_Root_Category_GeneratorModel');
            $rowset = array();
            $languages = null;
            $searchSelect = array('ignoreVisible' => true);
            foreach ($model->getRows($select) as $row) {
                $component = $root->getComponentById($row->id, $searchSelect);
                if (!$component) continue;
                if (is_null($languages)) {
                    $c = $component;
                    while ($c && !Vpc_Abstract::getFlag($c->componentClass, 'hasLanguage')) {
                        $c = $c->parent;
                    }
                    if ($c) {
                        $languagesModel = $c->parent->getComponent()->getChildModel();
                        foreach ($languagesModel->getRows() as $language) {
                            $data = $root->getComponentById($language->component_id, $searchSelect);
                            if ($data) {
                                $languages[] = $data;
                            }
                        }
                    } else {
                        $languages = array();
                    }
                }
                $rowset[] = $component;
                foreach ($languages as $language) {
                    $rowset[] = Vpc_Chained_Trl_Component::getChainedByMaster($component, $language, $searchSelect);
                }
            }
            if ($languages) {
                $model = Vps_Model_Abstract::getInstance('Vpc_Root_Category_Trl_GeneratorModel');
                $s = clone $select;
                $s->unsetPart('order');
                foreach ($model->getRows($s) as $row) {
                    $rowset[] = $root->getComponentById($row->component_id, $searchSelect);
                }
            }
        } else if ($parts && in_array('parent_id', $parts)) {
            $rowset = array($root);
        } else if (isset($where['parent_id'])) {
            $page = $root->getComponentById($where['parent_id'], array('ignoreVisible' => true));
            if (!$page) {
                throw new Vps_Exception("Can't find page with id '{$where['parent_id']}'");
            }
            $rowset = $page->getChildComponents(array(
                'ignoreVisible' => true,
                'generatorFlags' => array('showInPageTreeAdmin' => true)
            ));
            $rowset = array_values($rowset);
        } else if (isset($where['componentId'])) {
            $rowset = array(Vps_Component_Data_Root::getInstance()->getComponentById($where['componentId'], array('ignoreVisible' => true)));
        } else {
            throw new Vps_Exception('Cannot return all pages');
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
        return count($this->getRows($where));
    }

    public function getPrimaryKey()
    {
        return 'componentId';
    }

    public function getTable()
    {
        return Vps_Model_Abstract::getInstance('Vpc_Root_Category_GeneratorModel')->getTable();
    }

    protected function _getOwnColumns()
    {
        return array('componentId', 'parent_id', 'pos', 'visible', 'name', 'is_home');
    }

    public function getUniqueIdentifier()
    {
        throw new Vps_Exception("no unique identifier set");
    }
}