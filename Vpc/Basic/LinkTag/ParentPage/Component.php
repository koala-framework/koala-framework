<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_ParentPage_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_ParentPage_Data';
        $ret['componentName'] = trlVps('Link.to parent page');
        return $ret;
    }
}
