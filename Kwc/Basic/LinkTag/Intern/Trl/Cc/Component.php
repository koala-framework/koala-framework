<?php
class Kwc_Basic_LinkTag_Intern_Trl_Cc_Component extends Kwc_Chained_Cc_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_Intern_Trl_Cc_Data';
        return $ret;
    }
}
