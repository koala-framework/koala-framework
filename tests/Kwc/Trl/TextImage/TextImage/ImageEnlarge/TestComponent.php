<?php
class Kwc_Trl_TextImage_TextImage_ImageEnlarge_TestComponent extends Kwc_TextImage_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Trl_TextImage_TextImage_ImageEnlarge_TestModel';
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Trl_TextImage_TextImage_ImageEnlarge_LinkTag_TestComponent';
        return $ret;
    }

}
