<?php
class Vpc_Events_Directory_Admin extends Vpc_News_Directory_Admin
{

    protected function _init()
    {
        parent::_init();
        $this->_resourceName = trlVps('Events');
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['idTemplate'] = 'events_{0}-content';
        return $ret;
    }
}
