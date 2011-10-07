<?php
class Kwc_TextImage_Text_TestComponent extends Kwc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_TextImage_Text_TestModel';
        $ret['stylesModel'] = 'Kwc_TextImage_Text_TestStylesModel';
        $ret['generators']['child']['model'] = 'Kwc_TextImage_Text_ChildComponentsModel';
        $ret['generators']['child']['component'] = array(
            'image'         => null,
            'link'          => null,
            'download'      => null
        );
        return $ret;
    }

}
