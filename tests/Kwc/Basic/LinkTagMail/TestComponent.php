<?php
class Kwc_Basic_LinkTagMail_TestComponent extends Kwc_Basic_LinkTag_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_LinkTagMail_TestModel';
        return $ret;
    }
}
