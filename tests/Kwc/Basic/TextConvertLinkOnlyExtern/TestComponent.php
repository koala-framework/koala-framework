<?php
class Kwc_Basic_TextConvertLinkOnlyExtern_TestComponent extends Kwc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_TextConvertLinkOnlyExtern_TestModel';
        $ret['generators']['child']['component'] = array();
        $ret['generators']['child']['component']['link'] = 'Kwc_Basic_TextConvertLinkOnlyExtern_LinkExtern_Component';
        return $ret;
    }

    public function getRow()
    {
        return $this->_getRow();
    }

}
