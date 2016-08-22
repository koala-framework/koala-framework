<?php
class Kwc_Basic_LinkTag_Mail_Trl_Component extends Kwc_Basic_LinkTag_Abstract_Trl_Component
{
    public static function getSettings($masterComponent = null)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_Mail_Trl_Data';
        return $ret;
    }

}
