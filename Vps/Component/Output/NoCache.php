<?php
class Vps_Component_Output_NoCache extends Vps_Component_Output_Abstract
{
    protected function _render($componentId, $componentClass, $masterTemplate = false, array $plugins = array())
    {
        $ret = $this->_processComponent($componentId, $componentClass, $masterTemplate, $plugins);
        $ret = $this->_processAfterPlugins($ret);
        return $ret;
    }

    protected final function _processAfterPlugins($ret)
    {
        $ret = preg_replace_callback('#{afterPlugin ([^ ]+) ([^ ]+)}(.*){/afterPlugin}#ms',
                    array($this, '_processAfterPluginsCallback'), $ret);
        return $ret;
    }

    private function _processAfterPluginsCallback($m)
    {
        $plugin = new $m[1]($m[2]);
        $output = $m[3];
        $output = $this->_executeOutputPlugin($plugin, $output);
        $output = $this->_parseTemplate($output);
        return $output;
    }

    protected function _processComponent($componentId, $componentClass, $masterTemplate = false, array $plugins = array())
    {
        $beforePlugins = array();
        $afterPlugins = array();
        foreach ($plugins as $p) {
            if (!$p) throw new Vps_Exception("Invalid Plugin specified '$p'");
            $p = new $p($componentId);
            if (!$p instanceof Vps_Component_Plugin_Abstract)
                throw Vps_Exception('Plugin must be Instanceof Vps_Component_Plugin_Abstract');
            if ($p->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_BEFORE) {
                $beforePlugins[] = $p;
            } else if ($p->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_AFTER) {
                $afterPlugins[] = $p;
            }
        }

        $ret = $this->_renderContent($componentId, $componentClass, $masterTemplate, $afterPlugins);
        foreach ($beforePlugins as $plugin) {
            $ret = $this->_executeOutputPlugin($plugin, $ret);
        }
        $ret = $this->_parseDynamic($ret, $componentClass);
        $ret = $this->_parseTemplate($ret);
        foreach ($afterPlugins as $plugin) {
            $ret = "{afterPlugin ".get_class($plugin)." $componentId}".$ret."{/afterPlugin}";
        }
        return $ret;
    }

    protected function _executeOutputPlugin($plugin, $output)
    {
        return $plugin->processOutput($output);
    }

    protected function _parseTemplate($ret)
    {
        // partials-Tags ersetzen
        preg_match_all('/{partials: ([^ }]+) ([^ }]+) ([^ }]+) ([^ ]+) }/', $ret, $matches);
        foreach ($matches[0] as $key => $search) {
            $componentId = $matches[1][$key];
            $componentClass = $matches[2][$key];
            $partialsClass = $matches[3][$key];
            $params = unserialize($matches[4][$key]);
            $partial = new $partialsClass($params);
            $ids = $partial->getIds();
            $content = '';
            $number = 0; $count = count($ids);
            foreach ($ids as $id) {
                $info = array(
                    'total' => $count,
                    'number' => $number++
                );
                $content .= $this->_renderPartial($componentId, $componentClass, $partial, $id, $info);
            }
            $ret = str_replace($search, $content, $ret);
        }

        // hasContent-Tags ersetzen
        preg_match_all("/{content(No)?: ([^ }]+) ([^ }]+) ([^ }]+)}(.*){content(No)?}/imsU", $ret, $matches);
        foreach ($matches[0] as $key => $search) {
            $inverse = $matches[1][$key] == 'No';
            $componentId = $matches[3][$key];
            $componentClass = $matches[2][$key];
            $counter = $matches[4][$key];
            $content = $matches[5][$key];
            $replace = $this->_renderHasContent($componentId, $componentClass, $content, $counter, $inverse);
            $ret = str_replace($search, $replace, $ret);
        }

        // nocache-Tags ersetzen
        preg_match_all('/{nocache: ([^ }]+) ([^ }]*) ?([^}]*)}/', $ret, $matches);
        foreach ($matches[0] as $key => $search) {
            $componentId = $matches[2][$key];
            $componentClass = $matches[1][$key];
            $plugins = $matches[3][$key] ? explode(' ', trim($matches[3][$key])) : array();
            $replace = $this->_processComponent($componentId, $componentClass, false, $plugins);
            $ret = str_replace($search, $replace, $ret);
        }

        return $ret;
    }

    protected function _parseDynamic($ret, $componentClass, $info = array())
    {
        preg_match_all('/{dynamic: ([^ }]+) }(.*){\/dynamic}/U', $ret, $matches);
        foreach ($matches[0] as $key => $search) {
            $class = $matches[1][$key];
            $args = unserialize($matches[2][$key]);
            $info['componentClass'] = $componentClass;

            $dynamicClass = 'Vps_Component_Dynamic_' . $class;
            $dynamic = new $dynamicClass();
            $dynamic->setInfo($info);
            call_user_func_array(array($dynamic, 'setArguments'), $args);
            $replace = $dynamic->getContent();
            $ret = str_replace($search, $replace, $ret);
        }
        return $ret;
    }

    protected function _renderPartial($componentId, $componentClass, $partial, $id, $info, $useCache = false)
    {
        Vps_Benchmark::count('rendered partial ' . $useCache ? 'noviewcache' : 'nocache', $componentId);
        $output = new Vps_Component_Output_ComponentPartial();
        $output->setIgnoreVisible($this->ignoreVisible());
        $ret = $output->render($this->_getComponent($componentId), $partial, $id, $info);
        if (!$useCache) { //hack
            $ret = $this->_parseDynamic($ret, $componentClass, array('partial' => $info));
        }
        return $ret;
    }

    protected function _renderHasContent($componentId, $componentClass, $content, $counter, $inverse, $useCache = false)
    {
        Vps_Benchmark::count('rendered hascontent ' . $useCache ? 'noviewcache' : 'nocache', $componentId);
        $component = $this->_getComponent($componentId);
        $hasContent = $component->hasContent();
        return ($hasContent && !$inverse) || (!$hasContent && $inverse) ? $content : '';
    }

    protected function _renderContent($componentId, $componentClass, $masterTemplate, $afterPlugins = array(), $useCache = false)
    {
        Vps_Benchmark::count('rendered ' . $useCache ? 'noviewcache' : 'nocache', $componentId);
        if ($masterTemplate) {
            $output = new Vps_Component_Output_Master();
        } else {
            $output = new Vps_Component_Output_ComponentMaster();
        }
        $output->setIgnoreVisible($this->ignoreVisible());
        $ret = $output->render($this->_getComponent($componentId));
        return $ret;
    }
}
