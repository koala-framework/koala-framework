<?php
class Vpc_TextImage_Text_TestComponent extends Vpc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_TextImage_Text_TestModel';
        $ret['stylesModel'] = 'Vpc_TextImage_Text_TestStylesModel';
        $ret['generators']['child']['model'] = 'Vpc_TextImage_Text_ChildComponentsModel';
        $ret['generators']['child']['component'] = array(
            'image'         => null,
            'link'          => null,
            'download'      => null
        );
        return $ret;
    }

}
