<?php
class Vpc_TextImage_ImageEnlarge_Component extends Vpc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['imageCaption'] = true;
        $ret['generators']['child']['component']['linkTag'] = 'Vpc_TextImage_ImageEnlarge_LinkTag_Component';
        return $ret;
    }
}
