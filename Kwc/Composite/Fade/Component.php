<?php
class Vpc_Composite_Fade_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Fade');
        $ret['assets']['dep'][] = 'VpsFadeElements';

        $ret['selector'] = '> div';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['selector'] = $this->_getSetting('selector');
        return $ret;
    }
}
