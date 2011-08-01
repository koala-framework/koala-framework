<?php
class Vpc_Basic_LinkTag_TestComponent extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_LinkTag_TestModel';
        $ret['generators']['child']['component'] = array();
        $ret['generators']['child']['component']['test'] = 'Vpc_Basic_LinkTag_TestLinkTag_Component';
        $ret['generators']['child']['component']['test2'] = 'Vpc_Basic_LinkTag_TestLinkTag2_Component';
        $ret['generators']['child']['component']['empty'] = 'Vpc_Basic_Empty_Component';
        return $ret;
    }
}
