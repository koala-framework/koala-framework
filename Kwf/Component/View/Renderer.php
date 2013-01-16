<?php
abstract class Kwf_Component_View_Renderer extends Kwf_Component_View_Helper_Abstract
{
    protected static function _getGroupedViewPlugins($componentClass)
    {
        $plugins = array();
        foreach (Kwc_Abstract::getSetting($componentClass, 'plugins') as $p) {
            if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_View')) {
                $executionPoint = call_user_func(array($p, 'getExecutionPoint'));
                $plugins[$executionPoint][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewReplace')) {
                $plugins['replace'][] = $p;
            }
        }
        return $plugins;
    }
    
    protected function _getRenderPlaceholder($componentId, $config = array(), $value = null, $type = null, $plugins = array())
    {
        if (!$type) $type = $this->_getType();
        if (!is_null($value)) $componentId .= '(' . $value . ')';
        if ($plugins) $componentId .= json_encode((object)$plugins);
        $config = base64_encode(serialize($config));
        return '{cc ' . "$type: $componentId $config" . '}';
    }

    protected function _getComponentById($componentId)
    {
        $ret = Kwf_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => true));
        if (!$ret) throw new Kwf_Exception("Can't find component '$componentId' for rendering");
        return $ret;
    }

    private function _getType()
    {
        $ret = substr(strrchr(get_class($this), '_'), 1);
        $ret = strtolower(substr($ret, 0, 1)).substr($ret, 1); //anfangsbuchstaben klein
        return $ret;
    }

    /**
     * wird für ungecachte komponenten aufgerufen
     *
     * wird nur aufgerufen wenn ungecached
     */
    public abstract function render($componentId, $config);

    /**
     * schreibt den cache, kann überschrieben werden um den cache zu deaktivieren
     *
     * wird nur aufgerufen wenn ungecached (logisch)
     */
    public function saveCache($componentId, $renderer, $config, $value, $content)
    {
        $component = $this->_getComponentById($componentId);
        $type = $this->_getType();

        // Content-Cache
        Kwf_Component_Cache::getInstance()->save(
            $component,
            $content,
            $renderer,
            $type,
            $value
        );

        return true;
    }

    /**
     * Kann die render ausgabe (die aus cache oder direkt aus render kommen kann)
     * anpassen.
     *
     * wird immer aufgerufen, auch wenn sie gecached ist
     */
    public function renderCached($cachedContent, $componentId, $config)
    {
        return $cachedContent;
    }

    public function enableCache()
    {
        return true;
    }
}
