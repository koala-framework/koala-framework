<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_LinkTag_Intern_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'dataClass' => 'Kwc_Basic_LinkTag_Intern_Data',
            'ownModel'     => 'Kwc_Basic_LinkTag_Intern_Model',
            'componentName' => trlKwfStatic('Link.Intern'),
        ));
        $ret['assetsAdmin']['dep'][] = 'KwfPageSelectField';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/LinkTag/Intern/AnchorField.js';

        $ret['apiContent'] = 'Kwc_Basic_LinkTag_Intern_ApiContent';
        $ret['apiContentType'] = 'linkIntern';
        return $ret;
    }
}
