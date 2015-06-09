<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_Space_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Space');
        $ret['componentCategory'] = 'content';
        $ret['componentPriority'] = 70;
        $ret['ownModel'] = 'Kwc_Basic_Space_Model';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['height'] = $this->_getRow()->height;
        return $ret;
    }
}
