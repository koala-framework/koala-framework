<?php
class Vpc_Forum_Thread_Moderate_Close_Admin extends Vpc_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row instanceof Vpc_Forum_Group_Row) {
            $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsByDbId($row->component_id.'_'.$row->id.'-moderate-close');
            Vps_Component_Cache::getInstance()->remove($components);
        }
    }
}
