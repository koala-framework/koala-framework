<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_LinkTag_ParentPage_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_ParentPage_Data';
        $ret['componentName'] = trlKwf('Link.to parent page');
        return $ret;
    }
}
