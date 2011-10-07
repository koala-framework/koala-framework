<?php
class Kwc_Basic_LinkTagNews_TestComponent extends Kwc_Basic_LinkTag_News_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_LinkTagNews_TestModel';
        return $ret;
    }
}
