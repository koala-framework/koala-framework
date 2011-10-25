<?php
class Kwc_Basic_LinkTagIntern_TestComponent extends Kwc_Basic_LinkTag_Intern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_LinkTagIntern_TestModel';
        return $ret;
    }
}
