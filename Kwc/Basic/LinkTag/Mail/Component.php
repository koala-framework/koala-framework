<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_LinkTag_Mail_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'dataClass' => 'Kwc_Basic_LinkTag_Mail_Data',
            'ownModel'     => 'Kwc_Basic_LinkTag_Mail_Model',
            'componentName' => 'Link.E-Mail',
            'default' => array()
        ));
        $ret['apiContent'] = 'Kwc_Basic_LinkTag_Mail_ApiContent';
        $ret['apiContentType'] = 'mail';
        return $ret;
    }
}
