<?php
abstract class Vps_Component_View_Renderer extends Vps_Component_View_Helper_Abstract
{
    protected function _getRenderPlaceholder($componentId, $config = array(), $value = null, $type = null, $plugins = array())
    {
        if (!$type) $type = $this->_getType();
        if ($value) $componentId .= '(' . $value . ')';
        if ($plugins) $componentId .= '[' . implode(' ', $plugins) . ']';
        $config = base64_encode(serialize($config));
        return '{' . "$type: $componentId $config" . '}';
    }

    protected function _getComponentById($componentId)
    {
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => true));
        if (!$ret) throw new Vps_Exception("Can't find component '$componentId' for rendering");
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
    public function saveCache($componentId, $config, $value, $content)
    {
        $component = $this->_getComponentById($componentId);
        $cacheSettings = $component->getComponent()->getViewCacheSettings();
        if (!$cacheSettings['enabled']) return false;
        $type = $this->_getType();

        Vps_Component_Cache::getInstance()->save(
            $component,
            $content,
            $type,
            $value
        );
        foreach ($component->getComponent()->getCacheMeta() as $m) {
            Vps_Component_Cache::getInstance()->saveMeta($component, $m);
        }

        $renderComponent = $this->_getRenderer()->getRenderComponent();
        $renderPageId = $renderComponent->getPage() ? $renderComponent->getPage()->componentId : null;
        $pageId = $component->getPage() ? $component->getPage()->componentId : null;
        if ($renderPageId != $pageId) {
            Vps_Component_Cache::getInstance()->savePreload($renderPageId, $componentId, $type);
        }

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

}
