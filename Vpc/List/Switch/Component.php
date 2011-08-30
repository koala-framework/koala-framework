<?php
class Vpc_List_Switch_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'vps/Vpc/List/Switch/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['generators']['child']['component'] = 'Vpc_List_Switch_Preview_Component';
        $ret['placeholder']['prev'] = trlVps('previous');
        $ret['placeholder']['next'] = trlVps('next');
        $ret['previewCssClass'] = '';

        // transition kann auch auf false gesetzt werden um "direkt" umzuschalten
        $ret['transition'] = array(
            'type'               => 'fade',   // possible values: fade, slide
            'duration'           => 0.8,      // use with types: fade, slide
            'easingOut'          => 'easeIn', // use with types: fade, slide
            'easingIn'           => 'easeIn'  // use with types: fade, slide
        );
        $ret['showArrows'] = true; // whether to show arrows at all or not
        $ret['hideArrowsAtEnds'] = false; // false = wenn man beim letzten element ankommt und auf "weiter" klickt, kommt man wieder zum ersten
        $ret['eyeCandyListClass'] = 'Vpc.List.Switch.Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['previewCssClass'] = $this->_getSetting('previewCssClass');
        $ret['options']['transition'] = $this->_getSetting('transition');
        $ret['options']['hideArrowsAtEnds'] = $this->_getSetting('hideArrowsAtEnds');
        $ret['options']['showArrows'] = $this->_getSetting('showArrows');
        $ret['options']['class'] = $this->_getSetting('eyeCandyListClass');

        $ret['items'] = array();
        foreach ($ret['listItems'] as $item) {
            $ret['items'][] = array(
                'preview' => $this->_getPreviewComponent($item['data']),
                'large' => $this->_getLargeComponent($item['data']),
                'class' => $item['class']
            );
        }

        return $ret;
    }

    protected function _getPreviewComponent($childComponent)
    {
        return $childComponent;
    }

    protected function _getLargeComponent($childComponent)
    {
        return $childComponent->getChildComponent('-large');
    }
}
