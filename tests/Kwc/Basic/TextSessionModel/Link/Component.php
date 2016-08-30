<?php
class Kwc_Basic_TextSessionModel_Link_Component extends Kwc_Basic_LinkTag_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Basic_TextSessionModel_Link_TestModel';
        $ret['generators']['child']['component'] = array();
        $ret['generators']['child']['component']['extern'] = 'Kwc_Basic_TextSessionModel_Link_Extern_Component';
        $ret['generators']['child']['component']['intern'] = 'Kwc_Basic_TextSessionModel_Link_Intern_Component';
        return $ret;
    }
}
