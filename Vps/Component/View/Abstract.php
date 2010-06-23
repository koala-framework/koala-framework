<?php
abstract class Vps_Component_View_Abstract extends Vps_View
{
    private $_ignoreVisible = false;
    private $_enableCache = false;
    private $_plugins = array();
    private $_renderComponentId;
    private $_params = array();

    public function setIgnoreVisible($ignoreVisible)
    {
        $this->_ignoreVisible = $ignoreVisible;
    }

    public function ignoreVisible()
    {
        return $this->_ignoreVisible;
    }

    public function setEnableCache($enableCache)
    {
        $this->_enableCache = $enableCache;
    }

    protected function _getComponent($componentId)
    {
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => $this->ignoreVisible()));
        if (!$ret) throw new Vps_Exception("Can't find component '$componentId' for rendering");
        return $ret;
    }

    protected function _getPlugins($component)
    {
        $ret = array();
        $componentId = $component->componentId;

        // Keine Plugins bei Startkomponente außer es ist die root
        if ($this->_renderComponentId == $componentId &&
            $componentId != Vps_Component_Data_Root::getInstance()->componentId
        ) return $ret;

        if (!isset($this->_plugins[$componentId])) {
            $this->_plugins[$componentId] = array();
            foreach ($component->getPlugins() as $pluginClass) {
                $plugin = new $pluginClass($componentId);
                if (!$plugin instanceof Vps_Component_Plugin_Abstract)
                    throw Vps_Exception('Plugin must be Instanceof Vps_Component_Plugin_Abstract');
                $this->_plugins[$componentId][] = $plugin;
            }
        }

        if (count($this->_plugins[$componentId])) {
            $ret = $this->_plugins[$componentId];
            $this->_plugins[$componentId] = array();
        }
        return $ret;
    }

    public function renderComponent($component)
    {
        $this->_plugins = array();
        $this->_renderComponentId = $component->componentId;
        $matches = array(
            array('{component}'),
            array('component'),
            array($component->componentId),
            array('')
        );
        $ret = '{component}';
        return $this->_render($ret, $matches);
    }

    public function render($template)
    {
        if ($template instanceof Vps_Component_Data) return $this->renderComponent($template);
        $ret = parent::render($template);
        return $this->_render($ret);
    }

    protected function _formatOutputConfig($outputConfig, $component)
    {
        return $outputConfig;
    }

    protected function _render($ret, $matches = array(array()))
    {
        $afterPlugins = array();
        do {
            foreach ($matches[0] as $key => $search) {
                $type = $matches[1][$key];
                $componentId = trim($matches[2][$key]);
                $config = trim($matches[3][$key]);
                $config = $config != '' ? explode(' ', trim($config)) : array();

                $component = $this->_getComponent($componentId);
                if (!$component) {
                    throw new Vps_Exception("Could not find component with id $componentId for rendering.");
                }

                $outputConfig = array(
                    'type' => $type,
                    'config' => $config,
                    'plugins' => array()
                );
                $outputConfig = $this->_formatOutputConfig($outputConfig, $component);

                $class = 'Vps_Component_Output_' . ucfirst($outputConfig['type']);
                $output = new $class();
                $content = $output->render($component, $outputConfig['config']);
                foreach ($outputConfig['plugins'] as $plugin) {
                    if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_BEFORE) {
                        $content = $plugin->processOutput($content);
                    } else if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_AFTER) {
                        $afterPlugins[] = $plugin;
                        $index = count($afterPlugins) - 1;
                        $content = "{plugin_$index}$content{/plugin_$index}";
                    }
                }
                $ret = str_replace($search, $content, $ret);
            }
            preg_match_all('/{([^ }]+): ([^ }]+)([^}]*)}/', $ret, $matches);
        } while ($matches[0]);

        foreach ($afterPlugins as $index => $plugin) {
            $name = "plugin_$index";
            $len = strlen($name) + 2;
            $start = strpos($ret, '{' . $name . '}');
            if ($start !== false) {
                $stop = strpos($ret, '{/' . $name . '}');
                $content = substr($ret, $start + $len, $stop - $start - $len);
                $content = $plugin->processOutput($content);
                $ret = substr($ret, 0, $start) . $content . substr($ret, $stop + $len + 1);
            }
        }
        return $ret;
    }

}
