<?php
class Vpc_Trl_TextImage_TextImage_Text_Component extends Vpc_Basic_Text_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Trl_TextImage_TextImage_Text_TestModel';
        $ret['generators']['child']['model'] = 'Vpc_Trl_TextImage_TextImage_Text_ChildComponentsModel';
        $ret['generators']['child']['component'] = array(
            'image'         => null,
            'link'          => null,
            'download'      => null
        );
        return $ret;
    }

}
