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
        $attrs = " href=\"".htmlspecialchars($url)."\"";
        if (!empty($config['cssClass'])) {
            $cssClass = $config['cssClass'];
            if (is_array($cssClass)) $cssClass = implode(' ', $cssClass);
            $attrs .= " class=\"$cssClass\"";
        }
        if (!empty($config['style'])) {
            $attrs .= " style=\"".htmlspecialchars($config['style'])."\"";
        }
        if (!empty($config['target'])) {
            $attrs .= ' target="'.htmlspecialchars($config['target']).'"';
        }
        if (!empty($config['title'])) {
            $attrs .= ' title="'.htmlspecialchars($config['title']).'"';
        }
        if (!empty($rel)) {
            $attrs .= ' rel="'.htmlspecialchars($rel).'"';
        }

        if (!empty($config['dataAttributes'])) {
            foreach ($config['dataAttributes'] as $k=>$i) {
                $attrs .= ' data-'.htmlspecialchars($k).'="' . htmlspecialchars($i) . '"';
            }
        }

        return "<a{$attrs}><span>$text</span></a>";
    }
}
