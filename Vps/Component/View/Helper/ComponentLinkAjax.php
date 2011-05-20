<?php
class Vps_Component_View_Helper_ComponentLinkAjax extends Vps_Component_View_Helper_ComponentLink
{
    public function componentLinkAjax(Vps_Component_Data $target, $switchConfig = array(), $text = null, $cssClass = null, $get = array(), $anchor = null)
    {
        $config = $this->_getConfig($target, $text, $cssClass, $get, $anchor);
        $switchConfig['sel'] = $target->componentClass . '-ComponentAjax';
        $config['switch'] = $switchConfig;
        return $this->_getRenderPlaceholder($target->componentId, $config);
    }

    public function renderCached($cachedContent, $componentId, $config)
    {
        $ret = '<div class="' . $config['switch']['sel'] . ' vpsComponentAjax">';
        $settings = str_replace("\"", "'", Zend_Json::encode($config['switch']));
        $ret .= '<input type="hidden" class="settings" value="' . $settings . '" />';
        $config['cssClass'][] = $config['switch']['sel'];
        $ret .= parent::renderCached($cachedContent, $componentId, $config);
        $ret .= '</div>';
        return $ret;
    }
}
