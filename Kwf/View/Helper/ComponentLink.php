<?php
class Kwf_View_Helper_ComponentLink extends Kwf_Component_View_Helper_Abstract
{
    /**
     * @param Kwf_Component_Data target page
     * @param string custom text, if empty component name will be used
     * @param config array: cssClass, get, anchor
     */
    public function componentLink($target, $text = null, $config = array())
    {
        if (!is_array($config)) $config = array('cssClass' => $config); //compatibility

        if ($target instanceof Kwf_Component_Data) {
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
        if ($this->_getRenderer() instanceof Kwf_View_MailInterface) {
            $url = '*redirect*' . $url . '*';
        }

        if (!empty($config['anchor'])) $url .= "#".$config['anchor'];
        $html = '';
        if (!empty($config['cssClass'])) {
            $cssClass = $config['cssClass'];
            if (is_array($cssClass)) $cssClass = implode(' ', $cssClass);
            $html .= " class=\"".htmlspecialchars($cssClass)."\"";
        }
        if (isset($config['id'])) {
            $html .= " id=\"".htmlspecialchars($config['id'])."\"";
        }

        return "<a href=\"".htmlspecialchars($url)."\" rel=\"".htmlspecialchars($rel)."\"$html>$text</a>";
    }

    public function getTargetPage($component)
    {
        $ret = $component->getPage();
        if (!$ret) return null;
        if (is_instance_of($ret->componentClass, 'Kwc_Basic_LinkTag_Abstract_Component')) {
            if (!$ret->getComponent()->hasContent()) {
                return null;
            }
        }
        return $ret;
    }

}
