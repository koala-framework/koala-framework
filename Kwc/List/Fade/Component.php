<?php
abstract class Kwc_List_Fade_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Fade images');
        $ret['assets']['dep'][] = 'KwfFadeElements';

        // $ret['generators']['child']['component'] muss gesetzt werden

        $ret['selector'] = '> div';

        // optional: wird ausgeblendet wenn nur ein fade-element existiert
        // und das hier angegebene element keinen inhalt hat
        $ret['textSelector'] = '';

        $ret['fadeConfig'] = array(
            'elementAccessDirect' => false, // a button for each element to acces
            'elementAccessPlayPause' => false, // a play pause button to break fade-switching
            //'elementAccessLinks' => false, // deprecated, sets both of above
            'elementAccessNextPrevious' => false, // a previous and next button to switch pictures
            'fadeDuration'       => 1.5,
            'fadeEvery'          => 7,
            'easingFadeOut'      => 'easeInQuad',
            'easingFadeIn'       => 'easeOutQuad',
            'startRandom'        => true,
//             'animationType'      => ['', 'slide'],
            'autoStart'          => true,
//             'direction'          => ['left'|'right'],
            'template' => false
        );

        $ret['fadeClass'] = 'Kwf.Fade.Elements';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['selector'] = $this->_getSetting('selector');
        $ret['textSelector'] = $this->_getSetting('textSelector');
        $ret['fadeClass'] = $this->_getSetting('fadeClass');
        $ret['fadeConfig'] = array(
            'fadeConfig' => $this->_getSetting('fadeConfig'),
            'selector' => $ret['selector'],
            'textSelector' => $ret['textSelector'],
            'fadeClass' => $ret['fadeClass']
        );
        if (isset($ret['fadeConfig']['fadeConfig']['template'])
            && $ret['fadeConfig']['fadeConfig']['template']) {
            $ret['fadeConfig']['fadeConfig']['template'] = $this->getData()
                ->trlStaticExecute($ret['fadeConfig']['fadeConfig']['template']);
        }
        return $ret;
    }
}
