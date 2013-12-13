<?php
class Kwc_List_Gallery_Component extends Kwc_Abstract_List_Columns_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Gallery');
        $ret['componentIcon'] = new Kwf_Asset('images.png');
        $ret['generators']['child']['component'] = 'Kwc_List_Gallery_Image_Component';
        $ret['cssClass'] = 'webStandard';
        $ret['pdfColumns'] = 1;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['downloadAll'] = $this->getData()->getChildComponent('-downloadAll');
        return $ret;
    }
}
