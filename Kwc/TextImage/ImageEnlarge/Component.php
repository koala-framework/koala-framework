<?php
class Kwc_TextImage_ImageEnlarge_Component extends Kwc_Basic_ImageEnlarge_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['rootElementClass'] .= ' kwfup-webStandard';
        $ret['imageCaption'] = true;
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_TextImage_ImageEnlarge_LinkTag_Component';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/TextImage/ImageEnlarge/ImageUploadField.js';
        $ret['defineWidth'] = true;
        return $ret;
    }
}
