<?php
abstract class Kwc_Basic_LinkTag_Lightbox_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => null
        );
        $ret['assets']['files'][] = 'kwf/Kwc/Basic/LinkTag/Lightbox/Component.js';
        $ret['assets']['dep'][] = 'KwfLightbox';
        $ret['assets']['dep'][] = 'ExtConnection';
        $ret['popupDefaultWidth'] = 400;
        $ret['popupDefaultHeight'] = 300;
        $ret['extConfig'] = 'Kwc_Abstract_Composite_ExtConfigChildConfigs';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $child = $this->getData()->getChildComponent('-child');
        $ret['child'] = $child;
        $ret = array_merge($ret, $this->_getPopupVars($child));
        return $ret;
    }

    protected function _getPopupVars($child)
    {
        return array(
            'width' => $this->_getSetting('popupDefaultWidth'),
            'height' => $this->_getSetting('popupDefaultHeight'),
            'url' => $child->url
        );
    }
}
