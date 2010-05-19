<?php
class Vpc_Chained_Trl_MasterAsChild_Admin extends Vpc_Admin
{
    private $_admin;

    protected function _init()
    {
        $class = Vpc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $this->_admin = Vpc_Admin::getInstance($class);
    }

    public function getExtConfig($type = self::EXT_CONFIG_DEFAULT)
    {
        $ret = $this->_admin->getExtConfig($type);
        foreach ($ret as $key => $val) {
            $ret[$key]['componentIdSuffix'] = '-child';
        }
        return $ret;
    }
}
