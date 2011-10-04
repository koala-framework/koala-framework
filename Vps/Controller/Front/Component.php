<?php
class Vps_Controller_Front_Component extends Vps_Controller_Front
{
    protected function _getDefaultWebRouter()
    {
        return new Vps_Controller_Router('admin');
    }
}
