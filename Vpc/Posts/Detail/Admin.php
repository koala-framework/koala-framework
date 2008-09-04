<?php
class Vpc_Posts_Detail_Admin extends Vpc_Abstract_Composite_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate();
        if ($row instanceof Vpc_Posts_Directory_Row) {
            Vps_Component_Cache::getInstance()->remove($this->_class, $row->component_id.'-'.$row->id);
        }
    }
}
