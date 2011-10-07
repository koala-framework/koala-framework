<?php
class Kwc_Abstract_List_Row extends Kwf_Model_Proxy_Row
{
    protected function _delete()
    {
        $componentClass = $this->getTable()->getComponentClass();
        $class = Kwc_Abstract::getChildComponentClass($componentClass, 'child');
        $admin = Kwc_Admin::getInstance($class);
        $admin->delete($this->component_id . '-' . $this->id);
    }
}
