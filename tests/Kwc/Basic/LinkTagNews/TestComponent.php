<?php
class Vpc_Basic_LinkTagNews_TestComponent extends Vpc_Basic_LinkTag_News_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_LinkTagNews_TestModel';
        return $ret;
    }
}
