<?php
class Kwc_Basic_LinkTag_ComponentClass_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $data = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($row->target_component_id, array('subroot' => $data));
        if (!$data) return '';
        return $data->name;
    }
}
