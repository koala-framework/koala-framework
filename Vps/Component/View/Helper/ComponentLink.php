<?php
class Vps_Component_View_Helper_ComponentLink extends Vps_Component_View_Renderer
{
    /**
     * @param Vps_Component_Data target page
     * @param string custom text, if empty component name will be used
     * @param config array: cssClass, get, anchor, skipComponentLinkModifiers
     */
    public function componentLink($target, $text = null, $config = array())
    {
        if (!is_array($config)) $config = array('cssClass' => $config); //compatibility

        if ($target instanceof Vps_Component_Data) {
            $config = $this->_getConfig($target, $text, $config);
            return $this->_getRenderPlaceholder($target->componentId, $config);
        } else {
            return $this->_getHelper()->componentLink($target, $text, $config);
        }
    }

    protected function _getConfig($target, $text, $config)
    {
        $ret = $config;
        $ret['targetComponentId'] = $target->componentId;
        $ret['text'] = $text;
        return $ret;
    }

    public function render($componentId, $config)
    {
        $targetComponent = $this->_getComponentById($config['targetComponentId']);
        $targetPage = $this->_getHelper()->getTargetPage($targetComponent);
        if (!$targetPage) return '';

        $componentLinkModifiers = array();
        if (Vpc_Abstract::getFlag($targetPage->componentClass, 'hasComponentLinkModifiers')) {
            $componentLinkModifiers = $targetPage->getComponent()->getComponentLinkModifiers();
        }
        return serialize(array($targetPage->url, $targetPage->rel, $targetPage->name, $componentLinkModifiers));
    }

    public function renderCached($cachedContent, $componentId, $config)
    {
        if (!$cachedContent) return '';


        $targetPage = unserialize($cachedContent);

        $componentLinkModifiers = $targetPage[3];
        $text = $config['text'] ? $config['text'] : $targetPage[2];
        foreach ($componentLinkModifiers as $s) {
            if ($s['type'] == 'appendLinkText') {
                $text .= $s['text'];
            }
        }
        $ret = $this->_getHelper()->getLink(
            $targetPage[0], $targetPage[1], $text,
            $config
        );
        foreach ($componentLinkModifiers as $s) {
            if ($s['type'] == 'appendText') {
                $ret .= $s['text'];
            } else if ($s['type'] == 'callback') {
                $ret = call_user_func($s['callback'], $ret, $s['text']);
            }
        }
        return $ret;
    }

    private function _getHelper()
    {
        $helper = new Vps_View_Helper_ComponentLink();
        $helper->setRenderer($this->_getRenderer());
        return $helper;
    }
}
