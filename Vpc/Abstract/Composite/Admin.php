<?php
class Vpc_Abstract_Composite_Admin extends Vpc_Admin
{
    public function setup()
    {
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        foreach ($classes as $class) {
            Vpc_Admin::getInstance($class)->setup();
        }
    }
    public function delete($componentId)
    {
        parent::delete($componentId);
        $classes = Vpc_Abstract::getSetting($this->_class, 'childComponentClasses');
        if (!Vpc_Abstract::getSetting($this->_class, 'tablename')) {
            //wenn komponente kein model hat unterkomponenten hier löschen
            //ansonsten erledigt das die row
            foreach ($classes as $k=>$i) {
                Vpc_Admin::getInstance($i)->delete($componentId.'-'.$k);
            }
        }
        $where = array();
        $ids = array();
        foreach ($classes as $k=>$i) {
            $ids[] = $this->_db->quote($componentId.'-'.$k);
        }
        if ($ids) {
            $where[] = 'db_id IN ('.implode(', ', $ids).')';
            $this->_db->delete('vps_tree_cache', $where);
        }
    }
}
