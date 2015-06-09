<?php
class Kwc_Composite_Fade_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Fade');
        $ret['assetsDefer']['dep'][] = 'KwfFadeElements';

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
