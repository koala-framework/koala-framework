<?php
class Vpc_Forum_Thread_Admin extends Vpc_Abstract_Composite_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row instanceof Vpc_Forum_Group_Row) {
            Vps_Component_Cache::getInstance()->remove($this->_class, $row->component_id.'_'.$row->id);
        }
    }
}
