<?php
abstract class Vps_Component_View_Renderer
{
    protected $_view;

    public function setView(Vps_View $view)
    {
        $this->_view = $view;
    }

    /**
     * @return Vps_Component_View
     */
    protected function _getView()
    {
        return $this->_view;
    }

    protected function _getRenderPlaceholder($componentId, $config = array(), $value = null, $type = null, $plugins = array())
    {
        if (!$type) $type = $this->_getType();
        if ($value) $componentId .= '(' . $value . ')';
        if ($plugins) $componentId .= '[' . implode(' ', $plugins) . ']';
        $config = base64_encode(serialize($config));
        return '{' . "$type: $componentId $config" . '}';
    }

    public function getComponent($componentId)
    {
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => true));
        if (!$ret) throw new Vps_Exception("Can't find component '$componentId' for rendering");
        return $ret;
    }

    private function _getType()
    {
        return strtolower(substr(strrchr(get_class($this), '_'), 1));
    }

    public abstract function render($componentId, $config, $view);

    public function saveCache($componentId, $config, $value, $content) {
        $component = $this->getComponent($componentId);
        $cacheSettings = $component->getComponent()->getViewCacheSettings();
        if (!$cacheSettings['enabled']) return false;

        Vps_Component_Cache::getInstance()->save(
            $component,
            $content,
            $this->_getType(),
            $value
        );
        foreach ($component->getComponent()->getCacheMeta() as $m) {
            Vps_Component_Cache::getInstance()->saveMeta($component, $m);
        }
        if ($this->_getView()) {
            $page = $component->getPage();
            $cPage = $this->_getView()->getRenderComponent()->getPage();
            if ($page && $cPage && $page->componentId != $cPage->componentId) {
                Vps_Component_Cache::getInstance()->savePreload($this->_renderComponent, $component);
            }
        }

        return true;
    }
}
