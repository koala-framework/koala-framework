<?php
class Vpc_Basic_LinkTag_Intern_Cc_Admin extends Vpc_Basic_LinkTag_Intern_Trl_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        if (!$data->getLinkedData()) return '';
        return $data->getLinkedData()->name;
    }
}
