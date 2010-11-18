<?php
class Vpc_Basic_LinkTag_ComponentClass_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $data = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->target_component_id, array('subroot' => $data));
        if (!$data) return '';
        return $data->name;
    }
}
