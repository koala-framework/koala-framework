<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_Space_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Space');
        $ret['ownModel'] = 'Vpc_Basic_Space_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['height'] = $this->_getRow()->height;
        return $ret;
    }
}
