<?php
class Kwc_Composite_SwitchDisplay_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Switch display');
        $ret['generators']['child']['component']['linktext'] =
            'Kwc_Composite_SwitchDisplay_LinkText_Component';
        $ret['generators']['child']['component']['content'] =
            'Kwc_Paragraphs_Component';
        $ret['cssClass'] = 'webStandard';
        $ret['assets']['dep'][] = 'KwfSwitchDisplay';
        $ret['extConfig'] = 'Kwc_Abstract_Composite_ExtConfigTabs';
        return $ret;
    }
}
