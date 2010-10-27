<?php
abstract class Vps_Component_Renderer_Abstract
{
    private $_enableCache = false;
    private $_stats = array();
    protected $_renderComponent;

    public function setEnableCache($enableCache)
    {
        $this->_enableCache = $enableCache;
    }

    public function renderComponent($component)
    {
        $this->_plugins = array();
        $this->_renderComponent = $component;
        $this->_stats = array(
            'rendered' => array(),
            'cacheSaved' => array(),
            'cachePreloaded' => array(),
            'cacheRendered' => array()
        );
        if ($this->_enableCache) {
            $this->_cache = Vps_Component_Cache::getInstance()->preload($component);
            foreach ($this->_cache as $type => $componentIds) {
                foreach ($componentIds as $componentId => $values) {
                    foreach ($values as $value => $null) {
                        $statId = $componentId;
                        if ($value) $statId .= ' (' . $value .')';
                        if ($type != 'component') $statId .= ': ' . $type;
                        $this->_stats['cachePreloaded'][] = $statId;
                    }
                }
            }
        }
        $view = $this->_getView();
        $ret = $this->render($view, $view->component($component));
        return $ret;
    }

    protected function _formatRenderInfo($type, $config) {}

    /**
     * Eigentliche Render-Schleife
     *
     * Parst in einer Schleife $ret und rendert die gleiche View immer wieder.
     * In der View können Daten über mehrere Render- und Helper-Aufrufe hinweg gespeichert
     * werden, deshalb immer die gleiche View.
     *
     * @param $view
     * @param $ret
     */
    public function render($view, $ret = null)
    {
        if ($view instanceof Vps_Component_Data) return $this->renderComponent($view);
        if (!$view instanceof Vps_View) throw new Vps_Exception('Need view for rendering');

        $pluginNr = 0;
        $stats = $this->_stats;

        // {type: componentId(value)[plugins] config}
        while (preg_match('/{([^ }]+): ([^ \[}\(]+)(\([^ }]+\))?(\[[^}]+\])?( [^}]*)}/', $ret, $matches)) {
            $type = $matches[1];
            $componentId = trim($matches[2]);
            $value = (string)trim($matches[3]); // Bei Partial partialId oder bei master component_id zu der das master gehört
            if ($value) $value = substr($value, 1, -1);
            $plugins = trim($matches[4]);
            if ($plugins) $plugins = explode(' ', substr($plugins, 1, -1));
            if (!$plugins) $plugins = array();
            $config = trim($matches[5]);
            $config = $config != '' ? unserialize(base64_decode($config)) : array();

            $statId = $componentId;
            if ($value) $statId .= " ($value)";
            if ($type != 'component') $statId .= ': ' . $type;

            if ($this->_enableCache && isset($this->_cache[$type][$componentId][$value])) {

                $content = $this->_cache[$type][$componentId][$value];
                $stats['cacheRendered'][] = $statId;
                $statType = 'cache';

            } else {

                $view->clearVars();
                $class = 'Vps_Component_View_Helper_' . ucfirst($type);
                $helper = new $class();
                $helper->setView($view);
                $content = $helper->render($componentId, $config);
                $stats['rendered'][] = $statId;

                if ($this->_enableCache && $helper->saveCache($componentId, $config, $value, $content)) {
                    $stats['cacheSaved'][] = $statId;
                    $statType = 'nocache';
                } else {
                    $statType = 'noviewcache';
                }
            }

            foreach ($plugins as $pluginClass) {
                $plugin = new $pluginClass($componentId);
                if (!$plugin instanceof Vps_Component_Plugin_Abstract)
                    throw Vps_Exception('Plugin must be Instanceof Vps_Component_Plugin_Abstract');
                if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_BEFORE) {
                    $content = $plugin->processOutput($content);
                } else if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_AFTER) {
                    $pluginNr++;
                    $content = "{plugin $pluginNr $pluginClass $componentId}$content{/plugin $pluginNr}";
                }
            }

            Vps_Benchmark::count("rendered $statType", $statId);
            $ret = str_replace($matches[0], $content, $ret);
        }

        while (preg_match('/{plugin (\d) ([^}]*) ([^}]*)}(.*){\/plugin \\1}/', $ret, $matches)) {
            $pluginClass = $matches[2];
            $plugin = new $pluginClass($matches[3]);
            $content = $plugin->processOutput($matches[4]);
            $ret = str_replace($matches[0], $content, $ret);
        }

        $this->_stats = $stats;
        return $ret;
    }

    public function getStats()
    {
        return $this->_stats;
    }

    protected function _getView()
    {
        $view = new Vps_Component_View();
        $view->setRenderComponent($this->_renderComponent);
        return $view;
    }
}
