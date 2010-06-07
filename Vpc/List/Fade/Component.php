<?php
abstract class Vpc_List_Fade_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Fade images');
        $ret['assets']['dep'][] = 'VpsFadeElements';

        // $ret['generators']['child']['component'] muss gesetzt werden

        $ret['selector'] = '> div';

        $ret['fadeConfig'] = array(
            'elementAccessLinks' => false,
            'fadeDuration'       => 1.5,
            'fadeEvery'          => 7,
            'easingFadeOut'      => 'easeIn',
            'easingFadeIn'       => 'easeIn',
            'startRandom'        => true
        );

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['selector'] = $this->_getSetting('selector');
        $ret['fadeConfig'] = $this->_getSetting('fadeConfig');
        return $ret;
    }
}
