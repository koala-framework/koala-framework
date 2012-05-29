<?php
class Kwc_Composite_Marquee_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Marquee');
        $ret['assets']['dep'][] = 'KwfMarqueeElements';

        $ret['selector'] = '> div';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['settings'] = $this->_getSettings();
        return $ret;
    }

    protected function _getSettings()
    {
        $ret = array();
        $ret['selector'] = $this->_getSetting('selector');
        $ret['scrollDelay'] = 50;
        $ret['scrollAmount'] = 1;
        $ret['scrollDirection'] = 'up';
        return $ret;
    }
}
