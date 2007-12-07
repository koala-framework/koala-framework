<?php
class Vpc_News_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _delete()
    {
        $class = $this->getTable()->getComponentClass();
        $admin = Vpc_Admin::getInstance($class);
        if ($admin) {
            $admin->delete($this->page_id, $this->component_key . '-' . $this->id);
        }
    }
}
