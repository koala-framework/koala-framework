<?php
class Vps_Component_Abstract_ToStringData extends Vps_Data_Abstract
{
    public function load($row)
    {
        //$row ist die von der parent, also zB der List
        $c = Vps_Component_Data_Root::getInstance()->getComponentById($row->component_id.'-'.$row->id);
        if (!$c) return '';
        $admin = Vpc_Admin::getInstance($c->componentClass);
        return $admin->componentToString($c);
    }
}
