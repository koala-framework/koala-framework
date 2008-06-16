<?php
class Vpc_TreeCache_StaticBox extends Vpc_TreeCache_Static
{
    protected function _getSelectFields($key)
    {
        $fields = parent::_getSelectFields($key);
        $fields['box'] = new Zend_Db_Expr(
            $this->_cache->getAdapter()->quote($this->_getBoxValue($key, 'box'))
        );
        $fields['box_priority'] = new Zend_Db_Expr(
            $this->_cache->getAdapter()->quote($this->_getBoxValue($key, 'priority'))
        );
        if ($this->_getBoxValue($key, 'inherit')) {
            $fields['tag'] = new Zend_Db_Expr($this->_cache->getAdapter()->quote('inherit'));
        }
        return $fields;
    }
    
    protected function _getBoxValue($key, $param)
    {
        $class = $this->_classes[$key];
        if ($param == 'priority') {
            return isset($class['priority']) ? $class['priority'] : 0 ;
        } else if ($param == 'box') {
            return isset($class['box']) ? $class['box'] : $class['id'];
        } else if ($param == 'inherit') {
           return (!isset($class['inherit']) || $class['inherit']); 
        }
        return null;
    }
    
    protected function _insertValues($fields, $select, $key, $boxComponentClass)
    {
        $box = $this->_getBoxValue($key, 'box');
        $priority = $this->_getBoxValue($key, 'priority');
        $parentClass = $boxComponentClass ? $boxComponentClass : $this->_class;
        $select->where("component_id NOT IN (SELECT parent_component_id
                                FROM vps_tree_cache
                                WHERE box='$box'
                                AND box_priority>$priority
                                AND parent_component_class='$parentClass')");
        $this->_db->query("REPLACE INTO vps_tree_cache
               (".implode(', ', array_keys($fields)).") ($select)");
    }
}
