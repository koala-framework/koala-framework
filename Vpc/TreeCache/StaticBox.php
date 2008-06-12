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
        if (!isset($class['inherit']) || $class['inherit']) {
            $fields['generated'] = new Zend_Db_Expr(
                $this->_cache->getAdapter()->quote(Vps_Dao_TreeCache::GENERATE_INHERIT_BOX)
            );
        }
        $fields['box_priority'] = new Zend_Db_Expr(
            $this->_cache->getAdapter()->quote($priority)
        );
        return $fields;
    }
    
    public function createMissingChilds($boxComponentClass = null)
    {
        //$sql = "DELETE FROM vps_tree_cache WHERE "
        return parent::createMissingChilds($boxComponentClass);
    }
}
