<?php
class Vps_Component_View_Helper_ComponentLightbox extends Vps_Component_View_Helper_ComponentLink
{
    public function componentLightbox(Vps_Component_Data $target, $lightboxConfig = array(), $text = null, $cssClass = null, $get = array(), $anchor = null)
    {
        if ($cssClass) $cssClass .= ' ';
        $lightboxConfig['sel'] = $target->componentClass . '-LightboxLink';
        $cssClass .= 'vpsLightbox ' . $lightboxConfig['sel'];
        $settings = str_replace("\"", "'", Zend_Json::encode($lightboxConfig));
        $text = '<input type="hidden" class="settings" value="' . $settings . '" />' . $text;
        $config = $this->_getConfig($target, $text, $cssClass, $get, $anchor);
        return $this->_getRenderPlaceholder($target->componentId, $config);
    }

    public function renderCached($cachedContent, $componentId, $config)
    {
        if (!$cachedContent) return '';
        $targetPage = explode(';', $cachedContent);
        if (!$config['text']) $config['text'] = $targetPage[2];
        return parent::renderCached($cachedContent, $componentId, $config);
    }
}
