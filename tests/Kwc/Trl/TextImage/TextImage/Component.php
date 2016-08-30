<?php
class Kwc_Trl_TextImage_TextImage_Component extends Kwc_TextImage_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Trl_TextImage_TextImage_TestModel';
        $ret['generators']['child']['component']['text'] = 'Kwc_Trl_TextImage_TextImage_Text_Component';
        $ret['generators']['child']['component']['image'] = 'Kwc_Trl_TextImage_TextImage_ImageEnlarge_TestComponent';
        return $ret;
    }
}
