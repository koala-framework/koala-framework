<?php
class Vpc_Menu_Abstract_MenuModel extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vpc_Menu_Abstract_MenuRow';
    protected $_menuComponent;

    public function setMenuComponent($menuComponent)
    {
        $this->_menuComponent = $menuComponent;
    }

    protected function _getMenuData($parentComponent)
    {
        $c = $this->_menuComponent;
        while ($c) {
            if ($c->componentId == $parentComponent->componentId) {
                return $this->_menuComponent->getComponent()->getMenuData();
            }
            $c = $c->parent;
        }
        return array();
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $equals = $where->getParts(Vps_Model_Select::WHERE_EQUALS);
        if (isset($equals['whereEquals']['parent_id'])) {
            $parentId = $equals['whereEquals']['parent_id'];
            $parentComponent = Vps_Component_Data_Root::getInstance()->getComponentById($parentId);
            $rowset = array_values($this->_getMenuData($parentComponent));
        } else {
            $rowset = array();
        }
        $ret = new $this->_rowsetClass(array(
            'dataKeys' => $rowset,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
        return $ret;
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

    public function getPrimaryKey()
    {
        return 'componentId';
    }

    protected function _getOwnColumns()
    {
        return array('componentId', 'parent_id', 'name');
    }
}