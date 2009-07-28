<?php
class Vps_Controller_Front_Component extends Vps_Controller_Front
{
    protected function _getDefaultWebRouter()
    {
        return new Vps_Controller_Router('admin');
    }

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_instance->_init();
        }

        return self::$_instance;
    }
}
