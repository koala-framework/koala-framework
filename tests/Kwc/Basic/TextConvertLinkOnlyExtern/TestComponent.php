<?php
class Vpc_Basic_TextConvertLinkOnlyExtern_TestComponent extends Vpc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_TextConvertLinkOnlyExtern_TestModel';
        $ret['generators']['child']['model'] = 'Vpc_Basic_TextConvertLinkOnlyExtern_TestChildComponentsModel';
        $ret['generators']['child']['component'] = array();
        $ret['generators']['child']['component']['link'] = 'Vpc_Basic_TextConvertLinkOnlyExtern_LinkExtern_Component';
        return $ret;
    }

    public function getRow()
    {
        return $this->_getRow();
    }

}
