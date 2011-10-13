<?php
class Vps_View_Helper_ComponentLink extends Vps_Component_View_Helper_Abstract
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
            $target = $this->getTargetPage($target);
            if (!$text) $text = $target->name;
            $text = str_replace('{name}', $target->name, $text);
            return $this->getLink($target->url, $target->rel, $text, $config);
        } else {
            if (is_array($target)) {
                $url = $target['url'];
                $rel = isset($target['rel']) ? $target['rel'] : '';
            } else {
                $url = $target;
                $rel = '';
            }
            return $this->getLink($url, $rel, $text, $config);
        }
    }

    public function getLink($url, $rel, $text, $config)
    {
        if (!is_array($config)) $config = array('cssClass' => $config); //compatibility

        if (!empty($config['get'])) {
            $url .= '?';
            foreach ($config['get'] as $key => $val) $url .= "&$key=$val";
        }
        if ($this->_getRenderer() instanceof Vps_View_MailInterface) {
            $url = '*redirect*' . $url . '*';
        }

        if (!empty($config['anchor'])) $url .= "#".$config['anchor'];
        $cssClass = '';
        if (!empty($config['cssClass'])) {
            $cssClass = $config['cssClass'];
            if (is_array($cssClass)) $cssClass = implode(' ', $cssClass);
            $cssClass = " class=\"$cssClass\"";
        }

        return "<a href=\"".htmlspecialchars($url)."\" rel=\"".htmlspecialchars($rel)."\"$cssClass>$text</a>";
    }

    public function getTargetPage($component)
    {
        $ret = $component->getPage();
        if (is_instance_of($ret->componentClass, 'Vpc_Basic_LinkTag_Abstract_Component')) {
            if (!$ret->getComponent()->hasContent()) {
                return null;
            }
        }
        return $ret;
    }

}
