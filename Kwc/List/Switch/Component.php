<?php
class Kwc_List_Switch_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/List/Switch/Component.js';
        $ret['assets']['dep'][] = 'KwfList';
        $ret['generators']['child']['component'] = 'Kwc_List_Switch_Preview_Component';
        $ret['previewCssClass'] = '';

        // transition kann auch auf false gesetzt werden um "direkt" umzuschalten
        $ret['transition'] = array(
            'type'               => 'fade',   // possible values: fade, slide
            'duration'           => 0.8,      // use with types: fade, slide
        );
        $ret['showArrows'] = true; // whether to show arrows at all or not
        $ret['eyeCandyListClass'] = 'Kwc.List.Switch.Component';
        return $ret;
    }

    public static function validateSettings($settings)
    {
        parent::validateSettings($settings);
        if (isset($settings['hideArrowsAtEnds'])) {
            throw new Vps_Exception('hideArrowsAtEnds setting got removed, hide them using css (.listSwitchEnd)');
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['previewCssClass'] = $this->_getSetting('previewCssClass');
        $ret['options']['transition'] = $this->_getSetting('transition');
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
