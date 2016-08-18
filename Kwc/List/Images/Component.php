<?php
class Kwc_List_Images_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Images');
        $ret['componentIcon'] = 'pictures';
        $ret['generators']['child']['component'] = 'Kwc_Basic_Image_Component';
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['pdfColumns'] = 1;
        $ret['contentMargin'] = 10;
        return $ret;
    }
}
