<?php
class Kwc_Trl_TextImage_TextImage_Text_Component extends Kwc_Basic_Text_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Trl_TextImage_TextImage_Text_TestModel';
        $ret['generators']['child']['model'] = 'Kwc_Trl_TextImage_TextImage_Text_ChildComponentsModel';
        $ret['generators']['child']['component'] = array(
            'image'         => null,
            'link'          => null,
            'download'      => null
        );
        return $ret;
    }

}
