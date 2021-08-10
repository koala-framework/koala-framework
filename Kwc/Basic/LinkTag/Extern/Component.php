<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_LinkTag_Extern_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'dataClass' => 'Kwc_Basic_LinkTag_Extern_Data',
            'ownModel'     => 'Kwc_Basic_LinkTag_Extern_Model',
            'componentName' => trlKwfStatic('Link.Extern'),
            'hasPopup'      => true, //TODO: bezeichnung von diesem setting ist scheiße
            'openType'      => null, //wenn hasPopup auf false
        ));
        return $ret;
    }

}
