<?php
class Kwc_Basic_Text_TestComponent extends Kwc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Text_TestModel';
        $ret['stylesModel'] = 'Kwc_Basic_Text_TestStylesModel';
        $ret['generators']['child']['model'] = 'Kwc_Basic_Text_TestChildComponentsModel';
        $ret['generators']['child']['component']['link'] = 'Kwc_Basic_Text_Link_TestComponent';
        $ret['generators']['child']['component']['image'] = 'Kwc_Basic_Text_Image_TestComponent';
        $ret['generators']['child']['component']['download'] = 'Kwc_Basic_Text_Download_TestComponent';
        $ret['assets']['files']['styles'] = 'dynamic/Kwc_Basic_Text_StylesAsset:Kwc_Basic_Text_TestStylesModel';
        return $ret;
    }

    public function getRow()
    {
        return $this->_getRow();
    }

}
