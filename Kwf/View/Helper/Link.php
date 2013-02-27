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
            $url .= '?';
            foreach ($config['get'] as $key => $val) {
                $url .= "&$key";
                if ($val) { $url .= "=$val"; }
            }
        }

        if (!empty($config['anchor'])) $url .= "#".$config['anchor'];
        $cssClass = '';
        if (!empty($config['cssClass'])) {
            $cssClass = $config['cssClass'];
            if (is_array($cssClass)) $cssClass = implode(' ', $cssClass);
            $cssClass = " class=\"$cssClass\"";
        }
        if (!empty($config['style'])) {
            $cssClass .= " style=\"".$config['style']."\"";
        }
        $target = '';
        if (!empty($config['target'])) {
            $target = ' target="'.htmlspecialchars($config['target']).'"';
        }
        $title = '';
        if(!empty($config['title'])) {
            $title = ' title="'.htmlspecialchars($config['title']).'"';
        }

        return "<a href=\"".htmlspecialchars($url)."\"$title$target rel=\"".htmlspecialchars($rel)."\"$cssClass>$text</a>";
    }
}
