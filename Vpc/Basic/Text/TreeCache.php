<?php
class Vpc_Basic_Text_TreeCache extends Vpc_TreeCache_Table
{
    protected $_tableName = 'Vpc_Basic_Text_ChildComponentsModel';

    protected function _init()
    {
        $cls = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        foreach ($cls as $id=>$class) {
            if ($class) $this->_componentClass[$id] = $class;
        }
        parent::_init();
    }

    protected function _getSelectFields()
    {
        $fields = parent::_getSelectFields();

        $sql = '';
        foreach ($this->_componentClass as $type=>$c) {
            $type = $this->_cache->getAdapter()->quote($type);
            $c = $this->_cache->getAdapter()->quote($c);
            $sql .= "IF(t.type = $type, $c, ";
        }
        $sql .= "''";
        $sql .= str_repeat(')', count($this->_componentClass));
        $fields['component_class'] = new Zend_Db_Expr($sql);

        $sql = "CONCAT(tc.component_id, '-', LEFT(type, 1), nr)";
        $fields['component_id'] = new Zend_Db_Expr($sql);

        $sql = "CONCAT(tc.db_id, '-', LEFT(type, 1), nr)";
        $fields['db_id'] = new Zend_Db_Expr($sql);

        return $fields;
    }
}
