<?php
class Vps_Component_View extends Vps_View
{
    private $_ignoreVisible = false;
    private $_enableCache = false;
    private $_masterTemplates = null;
    private $_componentMasterTemplates = array();
    private $_plugins = array();
    private $_renderMaster = false;
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

    private function _getMasterTemplate($component)
    {
        $componentId = $component->componentId;
        if (is_null($this->_masterTemplates)) {
            $this->_masterTemplates = array();
            if ($componentId != Vps_Component_Data_Root::getInstance()->componentId) {
                $component = $component->parent;
            }
            while ($component) {
                $master = Vpc_Abstract::getTemplateFile($component->componentClass, 'Master');
                if ($master) $this->_masterTemplates[] = $master;
                $component = $component->parent;
            }
        }

        $ret = null;
        if (count($this->_masterTemplates))
            $ret = array_pop($this->_masterTemplates);
        return $ret;
    }

    private function _getComponentMasterTemplate($component, $renderMaster = true)
    {
        $componentId = $component->componentId;
        if ($componentId == Vps_Component_Data_Root::getInstance()->componentId)
            return null;
        if (!array_key_exists($componentId, $this->_componentMasterTemplates)) {
            $template = null;
            $template = Vpc_Abstract::getTemplateFile($component->componentClass, 'Master');
            $this->_componentMasterTemplates[$componentId] = $template;
        }

        $ret = null;
        if (!is_null($this->_componentMasterTemplates[$componentId])) {
            $ret = $this->_componentMasterTemplates[$componentId];
            $this->_componentMasterTemplates[$componentId] = null;
        }
        return $ret;
    }

    private function _getPlugins($component)
    {
        $componentId = $component->componentId;
        if (!isset($this->_plugins[$componentId])) {
            $this->_plugins[$componentId] = array();
            foreach ($component->getPlugins() as $pluginClass) {
                $plugin = new $pluginClass($componentId);
                if (!$plugin instanceof Vps_Component_Plugin_Abstract)
                    throw Vps_Exception('Plugin must be Instanceof Vps_Component_Plugin_Abstract');
                $this->_plugins[$componentId][] = $plugin;
            }
        }

        $ret = array();
        if (count($this->_plugins[$componentId])) {
            $ret = $this->_plugins[$componentId];
            $this->_plugins[$componentId] = array();
        }
        return $ret;
    }

    public function renderMaster($component)
    {
        return $this->renderComponent($component, true);
    }

    public function renderComponent($component, $renderMaster = false)
    {
        $this->_masterTemplates = null;
        $this->_componentMasterTemplates = array();
        $this->_plugins = array();
        $this->_renderMaster = $renderMaster;
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

                // Master
                if ($type == 'component' && $this->_renderMaster) {
                    $masterTemplate = $this->_getMasterTemplate($component);
                    if ($masterTemplate) {
                        $config = array($masterTemplate);
                        $type = 'master';
                    }
                }
                // Plugins
                $plugins = array();
                if ($type == 'component' &&
                    (   $this->_renderComponentId != $componentId || // keine Plugins bei Startkomponente außer es ist die root
                        $componentId == Vps_Component_Data_Root::getInstance()->componentId)
                    )
                {
                    $plugins = $this->_getPlugins($component);
                }
                // ComponentMaster
                if ($type == 'component') {
                    $componentMasterTemplate = $this->_getComponentMasterTemplate($component, $this->_renderMaster);
                    if ($componentMasterTemplate) {
                        $config = array($componentMasterTemplate);
                        $type = 'master';
                    }
                }

                $class = 'Vps_Component_Output_' . ucfirst($type);
                $output = new $class();
                $content = $output->render($component, $config, $this);
                foreach ($plugins as $plugin) {
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

    // Hier kann das Output was reinspeichern, was eventuell nachfolgende Outputs brauchen können (zB. Dynamic in Partials)
    public function setParam($param, $data)
    {
        $this->_params[$param] = $data;
    }

    public function getParam($param)
    {
        return isset($this->_params[$param]) ? $this->_params[$param] : null;
    }
}
