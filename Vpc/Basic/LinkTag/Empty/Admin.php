<?php
class Vpc_Basic_LinkTag_Empty_Admin extends Vpc_Abstract_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        return '('.trlVps('none').')';
    }
}
