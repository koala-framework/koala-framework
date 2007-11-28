<?php
class Vpc_Basic_Image_Enlarge_Row extends Vpc_Basic_Image_Row
{
    protected function _delete()
    {
        parent::_delete();
        $c = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'imageClass');
        $admin = Vpc_Admin::getInstance($c);
        $admin->delete($this->page_id, $this->component_key . '-' . $this->id);
    }
}
