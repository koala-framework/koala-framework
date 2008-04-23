<?php
class Vpc_TreeCache_StaticBox extends Vpc_TreeCache_Static
{
    protected function _getSelectFields($key)
    {
        $fields = parent::_getSelectFields($key);
        $class = $this->_classes[$key];
        $priority = isset($class['priority']) ? $class['priority'] : 0 ;
        $box = isset($class['box']) ? $class['box'] : $class['id'];
        $fields['box'] = new Zend_Db_Expr(
            $this->_cache->getAdapter()->quote($box)
        );
        $fields['box_priority'] = new Zend_Db_Expr(
            $this->_cache->getAdapter()->quote($priority)
        );
        return $fields;
    }
}
