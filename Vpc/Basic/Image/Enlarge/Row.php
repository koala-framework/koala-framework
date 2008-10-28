<?php
class Vpc_Basic_Image_Enlarge_Row extends Vpc_Basic_Image_Row
{
    protected function _delete()
    {
        parent::_delete();
        $componentClass = $this->getTable()->getComponentClass();
        $class = Vpc_Abstract::getChildComponentClass($componentClass, 'smallImage');
        $admin = Vpc_Admin::getInstance($class);
        if ($admin) {
            $admin->delete($this->component_id);
        }
    }
}
