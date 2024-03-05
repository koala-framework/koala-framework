<?php
class Kwf_View_Helper_Link
{
    protected $_view = null;
    public function setView($view)
    {
        $this->_view = $view;
    }


    /**
     * @param string target page
     * @param string custom text, if empty component name will be used
     * @param config array: cssClass, get, anchor
     */
    public function link($target, $text = null, $config = array())
    {
        if (!is_array($config)) $config = array('cssClass' => $config); //compatibility

        if (is_array($target)) {
            $url = $target['url'];
            $rel = isset($target['rel']) ? $target['rel'] : '';
            if (isset($target['dataAttributes'])) {
                if (!isset($config['dataAttributes'])) $config['dataAttributes'] = array();
                $config['dataAttributes'] = array_merge($config['dataAttributes'], $target['dataAttributes']);
            }
            if (isset($target['class'])) {
                if (!isset($config['cssClass'])) $config['cssClass'] = '';
                if ($config['cssClass']) $config['cssClass'] .= ' ';
                $config['cssClass'] .= $target['class'];
            }
        } else {
            $url = $target;
            $rel = '';
        }
        return $this->getLink($url, $rel, $text, $config);
    }

    public function getLink($url, $rel, $text, $config)
    {
        if (!is_array($config)) $config = array('cssClass' => $config); //compatibility

        if (!empty($config['get'])) {
            $url .= '?' . http_build_query($config['get']);
        }

        if (!empty($config['anchor'])) $url .= "#".$config['anchor'];
        $attrs = " href=\"".Kwf_Util_HtmlSpecialChars::filter($url)."\"";
        if (!empty($config['cssClass'])) {
            $cssClass = $config['cssClass'];
            if (is_array($cssClass)) $cssClass = implode(' ', $cssClass);
            $attrs .= " class=\"".Kwf_Util_HtmlSpecialChars::filter($cssClass)."\"";
        }
        if (!empty($config['style'])) {
            $attrs .= " style=\"".Kwf_Util_HtmlSpecialChars::filter($config['style'])."\"";
        }
        if (!empty($config['target'])) {
            $attrs .= ' target="'.Kwf_Util_HtmlSpecialChars::filter($config['target']).'"';
        }
        if (!empty($config['title'])) {
            $attrs .= ' title="'.Kwf_Util_HtmlSpecialChars::filter($config['title']).'"';
        }
        if (!empty($rel)) {
            $attrs .= ' rel="'.Kwf_Util_HtmlSpecialChars::filter($rel).'"';
        }

        if (!empty($config['dataAttributes'])) {
            foreach ($config['dataAttributes'] as $k=>$i) {
                $attrs .= ' data-'.Kwf_Util_HtmlSpecialChars::filter($k).'="' . Kwf_Util_HtmlSpecialChars::filter($i) . '"';
            }
        }

        return "<a{$attrs}>$text</a>";
    }
}
