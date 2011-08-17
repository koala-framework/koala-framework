<?php
abstract class Vps_Component_Renderer_Abstract
{
    protected $_enableCache = false;
    private $_renderComponent;

    public function setEnableCache($enableCache)
    {
        $this->_enableCache = $enableCache;
    }

    public function renderComponent($component)
    {
        $this->_renderComponent = $component;
        $content = $this->_renderComponentContent($component);
        $ret = $this->render($content);
        if ($this->_enableCache) {
            Vps_Component_Cache::getInstance()->writeBuffer();
        }
        return $ret;
    }

    protected function _renderComponentContent($component)
    {
        $masterHelper = new Vps_Component_View_Helper_Component();
        $masterHelper->setRenderer($this);
        return $masterHelper->component($component);
    }

    public function render($ret = null)
    {
        $pluginNr = 0;
        $helpers = array();

        // {cc type: componentId(value)[plugins] config}
        while (preg_match('/{cc ([a-z]+): ([^ \[}\(]+)(\([^ }]+\))?(\[[^}]+\])?( [^}]*)}/i', $ret, $matches)) {
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

            if (!isset($helpers[$type])) {
                $class = 'Vps_Component_View_Helper_' . ucfirst($type);
                $helper = new $class();
                $helper->setRenderer($this);
                $helpers[$type] = $helper;
            } else {
                $helper = $helpers[$type];
            }

            $statType = null;
            $content = null;
            $saveCache = true;
            if ($this->_enableCache) {
                $content = Vps_Component_Cache::NO_CACHE;
                if ($helper->enableCache()) {
                    $content = Vps_Component_Cache::getInstance()->load($componentId, $type, $value);
                    $statType = 'cache';
                }
                if ($content == Vps_Component_Cache::NO_CACHE) {
                    $content = null;
                    $saveCache = false;
                }
            }
            if (is_null($content)) {
                $content = $helper->render($componentId, $config);
                if ($saveCache && $helper->saveCache($componentId, $config, $value, $content)) {
                    $statType = 'nocache';
                } else {
                    $statType = 'noviewcache';
                }
            }
            $content = $helper->renderCached($content, $componentId, $config);

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

            if ($statType) Vps_Benchmark::count("rendered $statType", $statId);
            $ret = str_replace($matches[0], $content, $ret);
        }
        while (preg_match('/{plugin (\d) ([^}]*) ([^}]*)}(.*){\/plugin \\1}/s', $ret, $matches)) {
            $pluginClass = $matches[2];
            $plugin = new $pluginClass($matches[3]);
            $content = $plugin->processOutput($matches[4]);
            $ret = str_replace($matches[0], $content, $ret);
        }

        return $ret;
    }

    public function getRenderComponent()
    {
        return $this->_renderComponent;
    }
}
