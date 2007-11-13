<?php
class Vpc_Composite_Images_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _delete()
    {
        $c = Vpc_Abstract::getSetting($this->getTable()->getComponentClass(), 'enlargeClass');
        $admin = Vpc_Admin::getInstance($c);
        $admin->delete($this->page_id, $this->component_key . '-1');
    }
}