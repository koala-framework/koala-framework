<?php
class Kwc_Basic_Textfield_Trl_Component extends Kwc_Basic_Html_Trl_Component
{
    public static function getSettings($childComponentClass)
    {
        $ret = parent::getSettings($childComponentClass);
        $ret['ownModel'] = 'Kwc_Basic_Textfield_Model';
        return $ret;
    }
}
