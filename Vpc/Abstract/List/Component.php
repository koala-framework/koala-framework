<?php
abstract class Vpc_Abstract_List_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'List',
            'tablename'     => 'Vpc_Abstract_List_Model',
            'childComponentClasses' => array(),
            'showVisible' => true,
            'default' => array(
                'visible' => 1
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['children'] = array(); 
        foreach ($this->getChildComponentIds() as $id) {
            $ret['children'][] = $this->getComponentId() . '-' . $id;
        }
        return $ret;
    }

    //wird verwendet in Pdf Writer
    public function getChildComponentIds()
    {
        $table = $this->getTable();
        $select = $table->select();
        if (!$this->_showInvisible()) {
            $select->where('visible = 1');
        }
        $select->where('component_id = ?', $this->getDbID());
        $select->order('pos');

        $ret = array();
        foreach ($table->fetchAll($select) as $row) {
            $ret[] = $row->id;
        }
        return $ret;
    }

    public function getSearchVars()
    {
        $ret = parent::getSearchVars();
        foreach ($this->getChildComponents() as $c) {
            foreach ($c->getSearchVars() as $k=>$i) {
                if (!isset($ret[$k])) $ret[$k] = '';
                $ret[$k] .= ' '.$i;
            }
        }
        return $ret;
    }

    public function getStatisticVars()
    {
        $ret = parent::getStatisticVars();
        foreach ($this->getChildComponents() as $c) {
            $ret = array_merge($ret, $c->getStatisticVars());
        }
        return $ret;
    }

    public function getChildComponents()
    {
        if (!$this->_children) {
            $this->_children = array();
            $class = $this->_getClassFromSetting('child', 'Vpc_Abstract');
            $where = array(
                'component_id = ?' => $this->getDbId(),
                'component_class = ?' => $class
            );
            if (!$this->showInvisible()) {
                $where['visible = ?'] = 1;
            }

            $order = null;
            $tableInfo = $this->getTable()->info();
            if (in_array('pos', $tableInfo['cols'])) {
                $order = 'pos ASC';
            }
            foreach ($this->getTable()->fetchAll($where, $order) as $row) {
                $this->_children[$row->id] = $this->createComponent($class, $row->id);
            }
        }

        return $this->_children;
    }
}
