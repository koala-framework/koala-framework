<?php
class Kwc_Basic_TextConvertLinkOnlyExtern_LinkExtern_Component extends Kwc_Basic_LinkTag_Extern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_TextConvertLinkOnlyExtern_LinkExtern_TestModel';
        return $ret;
    }
}
