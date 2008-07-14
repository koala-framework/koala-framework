<?php
class Vpc_Basic_LinkTag_Row extends Vpc_Row
{
    protected function _update()
    {
        if ($this->_cleanData['component'] != $this->component) {
            $this->_delete();
        }
    }

    protected function _delete()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->getTable()->getComponentClass(), null, $this->component);
        $admin = Vpc_Admin::getInstance($class);
        if ($admin) {
            $admin->delete($this->component_id.'-1');
        }
    }
}
