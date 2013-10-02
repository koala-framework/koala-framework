<?php
class Kwc_Basic_LinkIntern_LinkTag_Component extends Kwc_Basic_LinkTag_Intern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_LinkIntern_LinkTag_Model';
        return $ret;
    }
}
