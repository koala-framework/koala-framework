<?php
class Vpc_Basic_Image_Enlarge_Row extends Vpc_Basic_Image_Row
{
    protected function _delete()
    {
        parent::_delete();
        $componentClass = $this->getTable()->getComponentClass();
        $classes = Vpc_Abstract::getSetting($componentClass, 'childComponentClasses');
        $admin = Vpc_Admin::getInstance($classes['enlarge']);
        if ($admin) {
            $admin->delete($this->page_id, $this->component_key);
        }
    }
}
