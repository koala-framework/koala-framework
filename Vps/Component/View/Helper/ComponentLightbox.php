<?php
class Vps_Component_View_Helper_ComponentLightbox extends Vps_Component_View_Helper_ComponentLink
{
    public function componentLightbox(Vps_Component_Data $target, $lightboxConfig = array(), $text = null, $cssClass = null, $get = array(), $anchor = null)
    {
        $lightboxConfig['sel'] = $target->componentClass . '-LightboxLink';
        $lightboxConfig['group'] = true;
        $config = array(
            'targetComponentId' => $target->componentId,
            'text' => $text,
            'cssClass' => $cssClass,
            'get' => $get,
            'anchor' => $anchor,
            'lightbox' => $lightboxConfig
        );
        return $this->_getRenderPlaceholder($target->componentId, $config);
    }

    public function renderCached($cachedContent, $componentId, $config)
    {
        $ret = '<div class="vpsLightbox">';
        $settings = str_replace("\"", "'", Zend_Json::encode($config['lightbox']));
        $ret .= '<input type="hidden" class="settings" value="' . $settings . '" />';
        $config['cssClass'] .= ' ' . $config['lightbox']['sel'];
        $ret .= parent::renderCached($cachedContent, $componentId, $config, $componentId);
        $ret .= '</div>';
        return $ret;
    }
}
