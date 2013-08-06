<?php
class Kwf_Component_Model extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwf_Component_Model_Row';
    protected $_primaryKey = 'componentId';
    private $_root;

    public function setRoot(Kwf_Component_Data $root)
    {
        $this->_root = $root;
    }

    public function getRoot()
    {
        if (!$this->_root) $this->_root = Kwf_Component_Data_Root::getInstance();
        return $this->_root;
    }

    public function createRow(array $data=array()) {
        throw new Kwf_Exception('Not implemented yet.');
    }

    public function isEqual(Kwf_Model_Interface $other) {
        if ($other instanceof Kwf_Component_Model &&
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

        $where = $select->getPart(Kwf_Model_Select::WHERE_EQUALS);
        $parts = $select->getPart(Kwf_Model_Select::WHERE_NULL);

        if ($select->hasPart(Kwf_Model_Select::WHERE_EXPRESSION)) { // Suchfeld
            return array(); // TODO: fix, didn't work before neither
            $model = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_GeneratorModel');
            $rowset = array();
            $languages = null;
            $searchSelect = array('ignoreVisible' => true);
            foreach ($model->getRows($select) as $row) {
                $component = $root->getComponentById($row->id, $searchSelect);
                if (!$component) continue;
                if (is_null($languages)) {
                    $c = $component;
                    while ($c && !Kwc_Abstract::getFlag($c->componentClass, 'hasLanguage')) {
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
                    $rowset[] = Kwc_Chained_Trl_Component::getChainedByMaster($component, $language, $searchSelect);
                }
            }
            if ($languages) {
                $model = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_Trl_GeneratorModel');
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
                throw new Kwf_Exception("Can't find page with id '{$where['parent_id']}'");
            }
            $rowset = $page->getChildComponents(array(
                'ignoreVisible' => true,
                'generatorFlags' => array('showInPageTreeAdmin' => true)
            ));
            $rowset = array_values($rowset);
        } else if (isset($where['componentId'])) {
            $rowset = array(Kwf_Component_Data_Root::getInstance()->getComponentById($where['componentId'], array('ignoreVisible' => true)));
        } else {
            throw new Kwf_Exception('Cannot return all pages');
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
        return Kwf_Model_Abstract::getInstance('Kwc_Root_Category_GeneratorModel')->getTable();
    }

    protected function _getOwnColumns()
    {
        return array('componentId', 'parent_id', 'pos', 'visible', 'name', 'is_home');
    }

    public function getUniqueIdentifier()
    {
        throw new Kwf_Exception("no unique identifier set");
    }
}