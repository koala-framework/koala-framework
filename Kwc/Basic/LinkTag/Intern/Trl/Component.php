<?php
class Kwc_Basic_LinkTag_Intern_Trl_Component extends Kwc_Basic_LinkTag_Abstract_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_Intern_Trl_Data';
        return $ret;
    }
}
