<?php
class Kwc_List_Images_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Images');
        $ret['componentIcon'] = new Kwf_Asset('pictures');
        $ret['generators']['child']['component'] = 'Kwc_Basic_Image_Component';
        $ret['cssClass'] = 'webStandard';
        $ret['pdfColumns'] = 1;
        $ret['contentMargin'] = 10;
        return $ret;
    }
}
