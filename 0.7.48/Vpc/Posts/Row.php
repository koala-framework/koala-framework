<?php
class Vpc_Posts_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _delete()
    {
        $class = $this->getTable()->getComponentClass();
        $admin = Vpc_Admin::getInstance($class);
        if ($admin) {
            $admin->delete($this->component_id . '-' . $this->id);
        }
    }
}
