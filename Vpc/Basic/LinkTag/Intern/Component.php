<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Intern_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'dataClass' => 'Vpc_Basic_LinkTag_Intern_Data',
            'modelname'     => 'Vpc_Basic_LinkTag_Intern_Model',
            'componentName' => trlVps('Link.Intern'),
        ));
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/LinkTag/Intern/LinkField.js';
        return $ret;
    }
}
