<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_LinkTag_Phone_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'dataClass' => 'Kwc_Basic_LinkTag_Phone_Data',
            'ownModel'     => 'Kwc_Basic_LinkTag_Phone_Model',
            'componentName' => 'Link.Phone',
            'default' => array()
        ));
        $ret['apiContent'] = 'Kwc_Basic_LinkTag_Phone_ApiContent';
        $ret['apiContentType'] = 'phone';
        return $ret;
    }
}
