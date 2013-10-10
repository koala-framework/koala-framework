<?php
class RedMallee_Box_FooterLogos_Links_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Links for Footer Logos');
        $ret['generators']['child']['component']['image'] = 'RedMallee_Box_FooterLogos_Links_Image_Component';
        return $ret;
    }
}
