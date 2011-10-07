<?php
class Kwc_Basic_LinkTagExtern_TestComponent extends Kwc_Basic_LinkTag_Extern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_LinkTagExtern_TestModel';
        return $ret;
    }
}
