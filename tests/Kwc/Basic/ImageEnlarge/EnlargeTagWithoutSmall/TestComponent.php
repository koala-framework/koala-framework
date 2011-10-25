<?php
class Kwc_Basic_ImageEnlarge_EnlargeTagWithoutSmall_TestComponent extends Kwc_Basic_ImageEnlarge_EnlargeTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_ImageEnlarge_TestModel';
        $ret['customPreviewImage'] = false;
        return $ret;
    }
}
