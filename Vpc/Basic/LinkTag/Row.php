<?php
class Vpc_Basic_LinkTag_Row extends Vps_Model_Proxy_Row
{
    protected function _update()
    {
        if ($this->_cleanData['component'] != $this->component) {
            $this->_delete();
        }
        parent::_update();
    }

    protected function _delete()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->getTable()->getComponentClass(), null, $this->component);
        $admin = Vpc_Admin::getInstance($class);
        if ($admin) {
            $admin->delete($this->component_id.'-link');
        }
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->component) {
            throw new Vps_Exception("component can not be empty");
        }
    }
}
