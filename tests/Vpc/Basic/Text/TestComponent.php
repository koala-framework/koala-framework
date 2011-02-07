<?php
class Vpc_Basic_Text_TestComponent extends Vpc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Text_TestModel';
        $ret['stylesModel'] = 'Vpc_Basic_Text_TestStylesModel';
        $ret['generators']['child']['model'] = 'Vpc_Basic_Text_TestChildComponentsModel';
        $ret['generators']['child']['component']['link'] = 'Vpc_Basic_Text_Link_TestComponent';
        $ret['generators']['child']['component']['image'] = 'Vpc_Basic_Text_Image_TestComponent';
        $ret['generators']['child']['component']['download'] = 'Vpc_Basic_Text_Download_TestComponent';
        return $ret;
    }

    public function getRow()
    {
        return $this->_getRow();
    }

}
