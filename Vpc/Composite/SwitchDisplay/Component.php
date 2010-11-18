<?php
class Vpc_Composite_SwitchDisplay_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Switch display');
        $ret['generators']['child']['component']['linktext'] =
            'Vpc_Composite_SwitchDisplay_LinkText_Component';
        $ret['generators']['child']['component']['content'] =
            'Vpc_Paragraphs_Component';
        $ret['cssClass'] = 'webStandard';
        $ret['assets']['dep'][] = 'VpsSwitchDisplay';
        return $ret;
    }
}
