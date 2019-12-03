<?php
class Kwc_Basic_ImageEnlarge_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Image Enlarge');
        $ret['componentIcon'] = 'imageEnlarge';
        $ret['generators']['child']['component']['linkTag'] = 'Kwc_Basic_ImageEnlarge_EnlargeTag_Component';
        $ret['generators']['child']['addUrlPart'] = false;
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/ImageEnlarge/ImageUploadField.js';
        $ret['rootElementClass'] = Kwf_Config::getValue('kwc.imageEnlarge.kwcClass'); //default is showHoverIcon
        $ret['apiContent'] = 'Kwc_Basic_ImageEnlarge_ApiContent';
        $ret['apiContentType'] = 'componentLink';
        return $ret;
    }
}
