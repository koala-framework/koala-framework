<?php
class Kwc_Basic_LinkTag_ParentPage_Trl_Component extends Kwc_Basic_LinkTag_Abstract_Trl_Component
{
    public static function getSettings($masterComponent = null)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_ParentPage_Data';
        return $ret;
    }
}
