<?php
class Kwc_Composite_Images_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Images');
        $ret['componentIcon'] = new Kwf_Asset('pictures');
        $ret['generators']['child']['component'] = 'Kwc_Basic_Image_Component';
        $ret['pdfColumns'] = 1;
        return $ret;
    }
}
