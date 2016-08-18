<?php
class Kwc_Basic_Link_Link_LinkTag_Component extends Kwc_Basic_LinkTag_Intern_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Basic_Link_Link_LinkTag_Model';
        return $ret;
    }
}
