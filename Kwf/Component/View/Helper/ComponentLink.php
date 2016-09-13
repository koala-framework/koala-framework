<?php
class Kwf_Component_View_Helper_ComponentLink extends Kwf_Component_View_Renderer
{
    /**
     * @param Kwf_Component_Data target page
     * @param string custom text, if empty component name will be used
     * @param config array: cssClass, get, anchor, skipAppendLinkText, skipAppendText, title
     */
    public function componentLink(Kwf_Component_Data $target, $text = null, $config = array())
    {
        if (!is_array($config)) $config = array('cssClass' => $config); //compatibility

        $config = $this->_getConfig($target, $text, $config);
        return $this->_getRenderPlaceholder($target->componentId, $config);
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
        $targetPage = $targetComponent->getPage();
        if (!$targetPage) return '';
        if (!$targetPage->url) {
            return '';
        }
        $componentLinkModifiers = array();
        if (!isset($config['skipComponentLinkModifiers']) || !$config['skipComponentLinkModifiers']) {
            if (Kwc_Abstract::getFlag($targetPage->componentClass, 'hasComponentLinkModifiers')) {
                $componentLinkModifiers = $targetPage->getComponent()->getComponentLinkModifiers();
            }
        }
        return serialize(array($targetPage->url, $targetPage->rel, $targetPage->name, $targetPage->getLinkDataAttributes(), $targetPage->getLinkTitle(), $componentLinkModifiers, $targetPage->getLinkClass()));
    }

    public function renderCached($cachedContent, $componentId, $config)
    {
        if (!$cachedContent) return '';

        $targetPage = unserialize($cachedContent);

        $config['dataAttributes'] = $targetPage[3];
        $config['title'] = $targetPage[4];
        $componentLinkModifiers = $targetPage[5];
        $linkClass = isset($targetPage[6]) ? $targetPage[6] : '';
        $text = $config['text'] ? $config['text'] : $targetPage[2];
        $text = str_replace('{name}', $targetPage[2], $text);
        if (!isset($config['skipAppendLinkText']) || !$config['skipAppendLinkText']) {
            foreach ($componentLinkModifiers as $s) {
                if ($s['type'] == 'appendLinkText') {
                    $text .= '<span class="appendText">'.$s['text'].'</span>';
                }
            }
        }

        $text = self::_replaceHtmlKwfUp($text);
        if (isset($config['cssClass'])) {
            if (is_array($config['cssClass'])) {
                foreach ($config['cssClass'] as &$i) {
                    $i = self::_replaceKwfUp($i);
                }
                unset($i);
            } else {
                $config['cssClass'] = self::_replaceKwfUp($config['cssClass']);
            }
        } else {
            $config['cssClass'] = '';
        }
        if ($linkClass) {
            $config['cssClass'] .= ' '.self::_replaceKwfUp($linkClass);
        }

        $url = $targetPage[0];
        if ($this->_getRenderer() instanceof Kwf_Component_Renderer_Mail) {
            $url = '*redirect*' . $url . '*';
        }

        $helper = new Kwf_View_Helper_Link();
        $ret = $helper->getLink(
            $url, $targetPage[1], $text,
            $config
        );
        if (!isset($config['skipAppendText']) || !$config['skipAppendText']) {
            foreach ($componentLinkModifiers as $s) {
                if ($s['type'] == 'appendText') {
                    $ret .= '<span class="appendText">'.$s['text'].'</span>';
                } else if ($s['type'] == 'callback') {
                    $ret = "<rcd $componentId ".$this->_getType().' '.json_encode($s).">$ret</rcd $componentId>";
                }
            }
        }
        return $ret;
    }

    public function renderCachedDynamic($cachedContent, $componentId, $settings)
    {
        return call_user_func($settings['callback'], $cachedContent, $componentId, $settings);
    }
}
