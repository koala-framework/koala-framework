<?php
class Kwc_Abstract_Cards_Row extends Kwf_Model_Proxy_Row
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
        $class = Kwc_Abstract::getChildComponentClass($this->getTable()->getComponentClass(), null, $this->component);
        $admin = Kwc_Admin::getInstance($class);
        if ($admin) {
            $admin->delete($this->component_id.'-child');
        }
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if (!$this->component) {
            throw new Kwf_Exception("component can not be empty");
        }
    }
}
