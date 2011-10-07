<?php
class Vpc_Abstract_List_Row extends Vps_Model_Proxy_Row
{
    protected function _delete()
    {
        $componentClass = $this->getTable()->getComponentClass();
        $class = Vpc_Abstract::getChildComponentClass($componentClass, 'child');
        $admin = Vpc_Admin::getInstance($class);
        $admin->delete($this->component_id . '-' . $this->id);
    }
}
