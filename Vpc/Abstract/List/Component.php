<?php
abstract class Vpc_Abstract_List_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'List',
            'tablename'     => 'Vpc_Abstract_List_Model',
            'childComponentClasses' => array(
                'child'         => 'Vpc_Empty'
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $tc = $this->getTreeCacheRow()->getTable();

        $class = $this->_getClassFromSetting('child', 'Vpc_Abstract');
        $where = array('parent_component_id = ?'=>$this->getDbID());
        $where['component_class = ?'] = $class;
        if (!$this->_showInvisible()) {
            $where['visible = ?'] = 1;
        }
        //todo: mit join optimieren - wenn wir Zend 1.5 haben
        $where[] = '(SELECT COUNT(*) FROM vpc_composite_list
            WHERE CONCAT(vpc_composite_list.component_id, \'-\', vpc_composite_list.id)
                    LIKE vps_tree_cache.component_id)';

        $return['children'] = array();
        foreach ($tc->fetchAll($where, 'pos') as $row) {
            $ret['children'][] = $row->component_id;
        }
        return $return;
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
