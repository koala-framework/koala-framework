<?php
class Kwc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_TestComponent extends Kwc_TextImage_ImageEnlarge_LinkTag_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_TextImage_ImageEnlarge_TestModel';
        
        return $ret;
    }
}
