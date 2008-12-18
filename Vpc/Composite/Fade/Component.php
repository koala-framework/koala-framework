<?php
class Vpc_Composite_Fade_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Gallery');
        $ret['assets']['dep'][] = 'VpsFadeElements';

        $ret['selector'] = '> div';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $selector = $this->_getSetting('selector');
        if (is_null($selector)) {
            throw new Vps_Exception("Setting 'selector' must be set when using an instance of 'Vpc_Composite_Fade_Component'");
        }
        $ret['selector'] = $selector;
        return $ret;
    }
}
