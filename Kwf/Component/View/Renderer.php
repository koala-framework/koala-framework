<?php
abstract class Kwf_Component_View_Renderer extends Kwf_Component_View_Helper_Abstract
{
    protected static function _getGroupedViewPlugins($componentClass)
    {
        $plugins = array();
        foreach (Kwc_Abstract::getSetting($componentClass, 'plugins') as $p) {
            if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewBeforeCache')) {
                $plugins['beforeCache'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewBeforeChildRender')) {
                $plugins['before'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewAfterChildRender')) {
                $plugins['after'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewReplace')) {
                $plugins['replace'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_UseViewCache')) {
                $plugins['useCache'][] = $p;
            }
        }
        return $plugins;
    }

    protected function _getRenderPlaceholder($componentId, $config = array(), $value = null, $plugins = array(), $viewCacheEnabled = true)
    {
        //is caching possible for this type? and is view cache enabled?
        $canBeIncludedInFullPageCache = $this->enableCache() && $viewCacheEnabled;

        $type = $this->_getType();

        $this->_getRenderer()->includedComponent($componentId, $type);

        if ($canBeIncludedInFullPageCache) {
            $pass = 1;
        } else {
            $pass = 2;
        }
        $plugins = $plugins ? json_encode((object)$plugins) : '';
        $config = $config ? base64_encode(serialize($config)) : '';
        return "<kwc$pass $type $componentId $value $plugins $config>";
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

        $settings = $component->getComponent()->getViewCacheSettings();
        if ($type != 'componentLink' && $type != 'master' && $type != 'page' && $type != 'fullPage' && !$settings['enabled']) {
            $content = Kwf_Component_Cache::NO_CACHE;
        }

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
