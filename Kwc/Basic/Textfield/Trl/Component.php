<?php
class Vpc_Basic_Textfield_Trl_Component extends Vpc_Basic_Html_Trl_Component
{
    public static function getSettings($childComponentClass)
    {
        $ret = parent::getSettings($childComponentClass);
        $ret['ownModel'] = 'Vpc_Basic_Textfield_Model';
        return $ret;
    }
}
