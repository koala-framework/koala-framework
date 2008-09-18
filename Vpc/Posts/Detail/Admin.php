<?php
class Vpc_Posts_Detail_Admin extends Vpc_Abstract_Composite_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row instanceof Vpc_Posts_Directory_Row) {
            $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsByDbId($row->component_id.'-'.$row->id);
            Vps_Component_Cache::getInstance()->remove($components);
        }
    }
}
