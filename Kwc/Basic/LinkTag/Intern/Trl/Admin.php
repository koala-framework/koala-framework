<?php
class Kwc_Basic_LinkTag_Intern_Trl_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        if (!$data->getLinkedData()) return '';
        return $data->getLinkedData()->name;
    }
}
