<?php
class Vpc_Basic_LinkTagMail_TestComponent extends Vpc_Basic_LinkTag_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_LinkTagMail_TestModel';
        return $ret;
    }
}
