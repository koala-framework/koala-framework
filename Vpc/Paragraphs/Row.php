<?php
class Vpc_Paragraphs_Row extends Vps_Db_Table_Row_Abstract
{
    protected function _delete()
    {
        $admin = Vpc_Admin::getInstance($this->component_class);
        if ($admin) {
            $admin->delete($this->component_id. '-' . $this->id);
        }
    }
}
