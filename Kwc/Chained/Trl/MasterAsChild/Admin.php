<?php
class Kwc_Chained_Trl_MasterAsChild_Admin extends Kwc_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $admin = Kwc_Admin::getInstance($data->chained->componentClass);
        return $admin->componentToString($data->getChildComponent('-child'));
    }
}
