<?php
class Vpc_Basic_Download_Trl_Admin extends Vpc_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        return $data->getComponent()->getRow()->infotext;
    }
}
