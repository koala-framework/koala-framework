<?php
class Vps_Component_View_Helper_ComponentLink extends Vps_Component_View_Renderer
{
    public function componentLink($target, $text = null, $cssClass = null, $get = array(), $anchor = null)
    {
        if ($target instanceof Vps_Component_Data) {
            $component = $this->_getView()->data;
            $config = array(
                'targetComponentId' => $target->componentId,
                'text' => $text,
                'cssClass' => $cssClass,
                'get' => $get,
                'anchor' => $anchor,
            );
            return $this->_getRenderPlaceholder($component->componentId, $config, $target->componentId);
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

    public function render($componentId, $config, $view)
    {
        $targetComponent = $this->getComponent($config['targetComponentId']);
        $targetPage = $targetComponent->getPage();
        if (is_instance_of($targetPage->componentClass, 'Vpc_Basic_LinkTag_Abstract_Component')) {
            if (!$targetPage->getComponent()->hasContent()) {
                return '';
            }
        }
        $text = $config['text'] ? $config['text'] : $targetPage->name;
        return $this->_getLink(
            $targetPage->url, $targetPage->rel, $text,
            $config['cssClass'], $config['get'], $config['anchor']
        );
    }
}
