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
            if (is_array($target)) {
                $url = $target['url'];
                $rel = isset($target['rel']) ? $target['rel'] : '';
            } else {
                $url = $target;
                $rel = '';
            }
            return $this->_getLink($url, $rel, $text, $cssClass, $get, $anchor);
        }
    }

    private function _getLink($url, $rel, $text, $cssClass, $get, $anchor)
    {
        if (!empty($get)) {
            $url .= '?';
            foreach ($get as $key => $val) $url .= "&$key=$val";
        }
        if ($anchor) $url .= "#$anchor";
        $cssClass = $cssClass ? " class=\"$cssClass\"" : '';
        return "<a href=\"$url\" rel=\"$rel\"$cssClass>$text</a>";
    }

    public function render($componentId, $config)
    {
        $targetComponent = $this->_getComponentById($config['targetComponentId']);
        $targetPage = $targetComponent->getPage();
        if (is_instance_of($targetPage->componentClass, 'Vpc_Basic_LinkTag_Abstract_Component')) {
            if (!$targetPage->getComponent()->hasContent()) {
                return '';
            }
        }
        return $targetPage->url.';'.$targetPage->rel.';'.$targetPage->name;
    }

    public function renderCached($cachedContent, $componentId, $config)
    {
        if (!$cachedContent) return '';

        $targetPage = explode(';', $cachedContent);

        $text = $config['text'] ? $config['text'] : $targetPage[2];
        return $this->_getLink(
            $targetPage[0], $targetPage[1], $text,
            $config['cssClass'], $config['get'], $config['anchor']
        );
    }

}
