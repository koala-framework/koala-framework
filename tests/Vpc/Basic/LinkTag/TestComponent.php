<?php
class Vpc_Basic_LinkTag_TestComponent extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_LinkTag_TestModel';
        $ret['generators']['link']['component'] = array();
        $ret['generators']['link']['component']['test'] = 'Vpc_Basic_LinkTag_TestLinkTag_Component';
        return $ret;
    }
}
