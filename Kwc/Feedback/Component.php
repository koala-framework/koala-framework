<?php
class Kwc_Feedback_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_Feedback_Form_Component';

        $ret['contentSender'] = 'Kwf_Component_Abstract_ContentSender_Lightbox';
        $ret['contentWidth'] = 520;

        $ret['editComponents'] = array('form');
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';

        $ret['cssClass'] = 'kwfup-webStandard';
        return $ret;
    }
}
