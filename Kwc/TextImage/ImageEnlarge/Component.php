<?php
class Kwc_TextImage_ImageEnlarge_Component extends Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] .= ' webStandard';
        $ret['imageCaption'] = true;
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_TextImage_ImageEnlarge_LinkTag_Component';
        return $ret;
    }
}
