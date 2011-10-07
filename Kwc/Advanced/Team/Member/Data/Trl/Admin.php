<?php
class Vpc_Advanced_Team_Member_Data_Trl_Admin extends Vpc_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        return $data->chained
            ->getComponent()
            ->getRow()
            ->__toString();
    }
}
