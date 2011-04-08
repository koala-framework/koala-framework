<?php
class Vps_Component_View_Helper_ComponentLink extends Vps_Component_View_Renderer
{
    public function componentLink($target, $text = null, $cssClass = null, $get = array(), $anchor = null)
    {
        if ($target instanceof Vps_Component_Data) {
            $config = array(
                'targetComponentId' => $target->componentId,
                'text' => $text,
                'cssClass' => $cssClass,
                'get' => $get,
                'anchor' => $anchor,
            );
            return $this->_getRenderPlaceholder($target->componentId, $config);
        } else {
            $helper = new Vps_View_Helper_ComponentLink();
            return $helper->componentLink($target, $text, $cssClass, $get, $anchor);
        }
    }

    public function render($componentId, $config)
    {
        $helper = new Vps_View_Helper_ComponentLink();
        $targetComponent = $this->_getComponentById($config['targetComponentId']);
        $targetPage = $helper->getTargetPage($targetComponent);
        if (!$targetPage) return '';
        return $targetPage->url.';'.$targetPage->rel.';'.$targetPage->name;
    }

    public function renderCached($cachedContent, $componentId, $config)
    {
        if (!$cachedContent) return '';

        $targetPage = explode(';', $cachedContent);

        $text = $config['text'] ? $config['text'] : $targetPage[2];
        $helper = new Vps_View_Helper_ComponentLink();
        return $helper->getLink(
            $targetPage[0], $targetPage[1], $text,
            $config['cssClass'], $config['get'], $config['anchor']
        );
    }

}
