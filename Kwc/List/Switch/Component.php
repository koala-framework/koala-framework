<?php
class Kwc_List_Switch_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['dep'][] = 'KwfList';
        $ret['assetsDefer']['dep'][] = 'ExtFx';
        $ret['generators']['child']['component'] = 'Kwc_List_Switch_Preview_Component';
        $ret['previewCssClass'] = '';

        $ret['generators']['itemPages'] = array(
            'class' => 'Kwc_List_Switch_ItemPageGenerator',
            'filenameColumn' => 'id',
            'uniqueFilename' => true,
            'nameColumn' => 'id',
            'component' => 'Kwc_List_Switch_ItemPage_Component',
            'showInMenu' => false,
        );
        $ret['plugins'] = array(
            'firstLargeContent' => 'Kwc_List_Switch_FirstLargeContentPlugin',
            'largeContent' => 'Kwc_List_Switch_LargeContentPlugin',
        );

        // transition kann auch auf false gesetzt werden um "direkt" umzuschalten
        $ret['transition'] = array(
            'type'               => 'fade',   // possible values: fade, slide
            'duration'           => 0.8,      // use with types: fade, slide
        );
        $ret['showArrows'] = true; // whether to show arrows at all or not
        $ret['showPlayPause'] = false; // whether to show a play/pause switcher or not
        $ret['autoPlay'] = false; // whether to start switching the contents automatically. only works if showPlayPause is true
        $ret['useHistoryState'] = true; // whether to add browser history entry if user navigates inside listŚwitch
        $ret['eyeCandyListClass'] = 'Kwc.List.Switch.Component';
        return $ret;
    }

    public static function validateSettings($settings)
    {
        parent::validateSettings($settings);
        if (isset($settings['hideArrowsAtEnds'])) {
            throw new Kwf_Exception('hideArrowsAtEnds setting got removed, hide them using css (.listSwitchEnd)');
        }
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['previewCssClass'] = $this->_getSetting('previewCssClass');
        $ret['options']['transition'] = $this->_getSetting('transition');
        $ret['options']['showArrows'] = $this->_getSetting('showArrows');
        $ret['options']['showPlayPause'] = $this->_getSetting('showPlayPause');
        $ret['options']['autoPlay'] = $this->_getSetting('autoPlay');
        $ret['options']['useHistoryState'] = $this->_getSetting('useHistoryState');
        $ret['options']['class'] = $this->_getSetting('eyeCandyListClass');

        foreach ($ret['listItems'] as &$item) {
            $item['largePage'] = $this->getData()->getChildComponent('_'.$item['data']->id);
        }
        return $ret;
    }

    public function getDefaultItemPage()
    {
        return $this->getData()
            ->getChildComponent(array('generator'=>'itemPages', 'limit'=>1));
    }

    public function getLargeComponent($itemPageComponent)
    {
        return $this->getData()
            ->getChildComponent('-'.$itemPageComponent->id)
            ->getChildComponent('-large');
    }

    public function getPreviewComponent($itemPageComponent)
    {
        return $this->getData()
            ->getChildComponent('-'.$itemPageComponent->id);
    }
}
