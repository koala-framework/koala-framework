<?php
class Kwc_Basic_LinkTag_TestComponent extends Kwc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_LinkTag_TestModel';
        $ret['generators']['child']['component'] = array();
        $ret['generators']['child']['component']['test'] = 'Kwc_Basic_LinkTag_TestLinkTag_Component';
        $ret['generators']['child']['component']['test2'] = 'Kwc_Basic_LinkTag_TestLinkTag2_Component';
        $ret['generators']['child']['component']['empty'] = 'Kwc_Basic_Empty_Component';
        return $ret;
    }
}
