<?php
class Kwc_Basic_Link_Link_LinkTag_Component extends Kwc_Basic_LinkTag_Intern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Link_Link_LinkTag_Model';
        return $ret;
    }
}
