<?php
class Vpc_Paragraphs_Row extends Vpc_Row
{
    protected function _delete()
    {
        $admin = Vpc_Admin::getInstance($this->component_class);
        if ($admin) {
            $admin->delete($this->component_id. '-' . $this->id);
        }
    }
}
