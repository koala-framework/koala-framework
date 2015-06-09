<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_LinkTag_Mail_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'dataClass' => 'Kwc_Basic_LinkTag_Mail_Data',
            'ownModel'     => 'Kwc_Basic_LinkTag_Mail_Model',
            'componentName' => 'Link.E-Mail',
            'default' => array()
        ));
        $ret['assetsDefer']['dep'][] = 'KwfMailDecode';
        return $ret;
    }
}
