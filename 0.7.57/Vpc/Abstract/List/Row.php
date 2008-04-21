<?php
class Vpc_Abstract_List_Row extends Vpc_Row
{
    protected function _delete()
    {
        $componentClass = $this->getTable()->getComponentClass();
        $classes = Vpc_Abstract::getSetting($componentClass, 'childComponentClasses');
        $admin = Vpc_Admin::getInstance($classes['child']);
        $admin->delete($this->component_id . '-' . $this->id);
    }
}
