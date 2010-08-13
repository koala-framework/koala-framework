<?php
abstract class Vps_Component_View_Renderer
{
    protected $_view;

    public function setView(Vps_View $view)
    {
        $this->_view = $view;
    }

    /**
     * @return Vps_View
     */
    protected function _getView()
    {
        return $this->_view;
    }

    public abstract function render($component, $config, $view);

    protected function _getCacheValue()
    {
        return '';
    }

    public function saveCache($component, $content) {
        $type = strtolower(substr(strrchr(get_class($this), '_'), 1));
        $ret = Vps_Component_Cache::getInstance()->save(
            $component,
            $content,
            $type,
            $this->_getCacheValue()
        );
        if ($ret) {
            foreach ($component->getComponent()->getCacheMeta() as $m) {
                Vps_Component_Cache::getInstance()->saveMeta($component, $m);
            }
        }
        return $ret;
    }
}
