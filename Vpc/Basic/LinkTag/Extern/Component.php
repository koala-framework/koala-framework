<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Extern_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'dataClass' => 'Vpc_Basic_LinkTag_Extern_Data',
            'ownModel'     => 'Vpc_Basic_LinkTag_Extern_Model',
            'componentName' => trlVps('Link.Extern'),
            'hasPopup'      => true
        ));
        $ret['assets']['files'][] = 'vps/Vpc/Basic/LinkTag/Extern/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        return $ret;
    }

}
