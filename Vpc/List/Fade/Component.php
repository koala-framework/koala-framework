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

        // optional: wird ausgeblendet wenn nur ein fade-element existiert
        // und das hier angegebene element keinen inhalt hat
        $ret['textSelector'] = '';

        $ret['fadeConfig'] = array(
            'elementAccessDirect' => false, // a button for each element to acces
            'elementAccessPlayPause' => false, // a play pause button to break fade-switching
            'elementAccessLinks' => false, // deprecated, sets both of above
            'elementAccessNextPrevious' => false, // a previous and next button to switch pictures
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
        $ret['textSelector'] = $this->_getSetting('textSelector');
        $ret['fadeConfig'] = $this->_getSetting('fadeConfig');
        return $ret;
    }
}
