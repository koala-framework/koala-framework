<?php
class Vpc_Chained_Trl_MasterAsChild_Admin extends Vpc_Abstract_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        $admin = Vpc_Admin::getInstance($data->chained->componentClass);
        return $admin->componentToString($data->getChildComponent('-child'));
    }
}
