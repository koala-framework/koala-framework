<?php
class Vps_Component_Data_Home extends Vps_Component_Data
{
    public function __get($var)
    {
        if ($var == 'url') {
            return '/';
        }
    }

}
