<?php
class Vpc_Flash_Flash_Component extends Vpc_Abstract_Flash_Component
{
    protected function _getFlashData()
    {
        $ret = parent::_getFlashData();
        $ret['url'] = '/assets/vps/tests/Vpc/Flash/Flash/demo.swf';
        $ret['width'] = 558;
        $ret['height'] = 168;
        return $ret;
    }

    protected function _getFlashVars()
    {
        $ret = parent::_getFlashVars();
        $ret['allowfullscreen'] = true;
        return $ret;
    }
}