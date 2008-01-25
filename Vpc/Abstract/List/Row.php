<?php
class Vpc_Abstract_List_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _delete()
    {
        $componentClass = $this->getTable()->getComponentClass();
        $classes = Vpc_Abstract::getSetting($componentClass, 'childComponentClasses');
        $admin = Vpc_Admin::getInstance($classes['child']);
        $admin->delete($this->page_id, $this->component_key . '-' . $this->id);
    }
}
