<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_Space_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Space');
        $ret['componentCategory'] = 'layout';
        $ret['componentPriority'] = 70;
        $ret['ownModel'] = 'Kwc_Basic_Space_Model';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['apiContent'] = 'Kwc_Basic_Space_ApiContent';
        $ret['apiContentType'] = 'space';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['height'] = $this->_getRow()->height;
        return $ret;
    }
}
