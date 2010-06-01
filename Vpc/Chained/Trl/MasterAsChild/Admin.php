<?php
class Vpc_Chained_Trl_MasterAsChild_Admin extends Vpc_Admin
{
    private $_admin;

    protected function _init()
    {
        $class = Vpc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $this->_admin = Vpc_Admin::getInstance($class);
    }
}
