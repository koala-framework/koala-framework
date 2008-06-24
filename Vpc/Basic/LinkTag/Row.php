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
        $classes = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'childComponentClasses');
        $admin = Vpc_Admin::getInstance($classes[$this->component]);
        if ($admin) {
            $admin->delete($this->component_id.'-1');
        }
    }
}
