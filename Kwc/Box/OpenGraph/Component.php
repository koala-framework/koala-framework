<?php
class Kwc_Box_OpenGraph_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Open Graph');
        $ret['componentIcon'] = new Kwf_Asset('cog');
        $ret['generators']['child']['component']['image'] = 'Kwc_Box_OpenGraph_Image_Component';
        $ret['flags']['hasHeaderIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }
}
