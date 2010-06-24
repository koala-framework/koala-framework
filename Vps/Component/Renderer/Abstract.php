<?php
abstract class Vps_Component_Renderer_Abstract
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
        $ret = Vps_Component_Output_Component::getHelperOutput($component);
        return $this->render($ret);
    }

    protected function _formatOutputConfig($outputConfig, $component)
    {
        return $outputConfig;
    }

    public function render($ret)
    {
        if ($ret instanceof Vps_Component_Data) return $this->renderComponent($ret);

        $view = $this->_getView();
        $pluginNr = 0;

        while (preg_match('/{([^ }]+): ([^ }]+)([^}]*)}/', $ret, $matches)) {
            $type = $matches[1];
            $componentId = trim($matches[2]);
            $config = trim($matches[3]);
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
            $view->clearVars();
            $content = $output->render($component, $outputConfig['config'], $view);
            foreach ($outputConfig['plugins'] as $plugin) {
                if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_BEFORE) {
                    $content = $plugin->processOutput($content);
                } else if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_AFTER) {
                    $pluginNr++;
                    $pluginClass = get_class($plugin);
                    $content = "{plugin $pluginNr $pluginClass $componentId}$content{/plugin $pluginNr}";
                }
            }
            $ret = str_replace($matches[0], $content, $ret);
        }

        while (preg_match('/{plugin (\d) ([^}]*) ([^}]*)}(.*){\/plugin \\1}/', $ret, $matches)) {
            $pluginClass = $matches[2];
            $plugin = new $pluginClass($matches[3]);
            $content = $plugin->processOutput($matches[4]);
            $ret = str_replace($matches[0], $content, $ret);
        }

        return $ret;
    }

    protected function _getView()
    {
        return new Vps_View();
    }
}
