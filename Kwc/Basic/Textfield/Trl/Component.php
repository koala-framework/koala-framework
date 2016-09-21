<?php
class Kwc_Basic_Textfield_Trl_Component extends Kwc_Basic_Html_Trl_Component
{
    public static function getSettings($childComponentClass = null)
    {
        $ret = parent::getSettings($childComponentClass);
        $ret['ownModel'] = 'Kwc_Basic_Textfield_Model';
        return $ret;
    }
}
