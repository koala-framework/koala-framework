<?php
class Kwc_Trl_LinkIntern_LinkTagIntern_Component extends Kwc_Basic_LinkTag_Intern_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Trl_LinkIntern_LinkTagIntern_TestModel';
        return $ret;
    }
}
