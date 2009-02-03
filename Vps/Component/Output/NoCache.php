<?php
class Vps_Component_Output_NoCache extends Vps_Component_Output_Abstract
{
    protected function _render($componentId, $componentClass, $masterTemplate = false, array $plugins = array())
    {
        return $this->_processComponent($componentId, $componentClass, $masterTemplate, $plugins);
    }

    protected function _processComponent($componentId, $componentClass, $masterTemplate = false, array $plugins = array())
    {
        $ret = $this->_renderContent($componentId, $componentClass, $masterTemplate);

        foreach ($plugins as $p) {
            if (!$p) throw new Vps_Exception("Invalid Plugin specified '$p'");
            $p = new $p($componentId);
            $ret = $p->processOutput($ret);
        }

        $ret = $this->_parseTemplate($ret);
        return $ret;
    }

    protected function _parseTemplate($ret)
    {
        // hasContent-Tags ersetzen
        preg_match_all("/{content: ([^ }]+) ([^ }]*)}(.*){content}/imsU", $ret, $matches);
        foreach ($matches[0] as $key => $search) {
            $componentId = $matches[2][$key];
            $componentClass = $matches[1][$key];
            $content = $matches[3][$key];
            $replace = $this->_renderHasContent($componentId, $componentClass, $content);
            $ret = str_replace($search, $replace, $ret);
        }

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
                    'number' => ++$number
                );
                $content .= $this->_renderPartial($componentId, $componentClass, $partial, $id, $info);
            }
            $ret = str_replace($search, $content, $ret);
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

    protected function _renderPartial($componentId, $componentClass, $partial, $id, $info)
    {
        if ($this->_hasViewCache($componentClass)) {
            Vps_Benchmark::count('rendered partial nocache', $componentId);
        } else {
            Vps_Benchmark::count('rendered partial', $componentId);
        }
        $output = new Vps_Component_Output_ComponentPartial();
        $output->setIgnoreVisible($this->ignoreVisible());
        return $output->render($this->_getComponent($componentId), $partial, $id, $info);
    }

    protected function _renderHasContent($componentId, $componentClass, $content)
    {
        if ($this->_hasViewCache($componentClass)) {
            Vps_Benchmark::count('rendered nocache', $componentId.' (hasContent)');
        } else {
            Vps_Benchmark::count('rendered noviewcache', $componentId.' (hasContent)');
        }
        $component = $this->_getComponent($componentId);
        return $component->hasContent() ? $content : '';
    }

    protected function _renderContent($componentId, $componentClass, $masterTemplate)
    {
        if ($this->_hasViewCache($componentClass)) {
            Vps_Benchmark::count('rendered nocache', $componentId.($masterTemplate?' (master)':''));
        } else {
            Vps_Benchmark::count('rendered noviewcache', $componentId.($masterTemplate?' (master)':''));
        }
        if ($masterTemplate) {
            $output = new Vps_Component_Output_Master();
        } else {
            $output = new Vps_Component_Output_ComponentMaster();
        }
        $output->setIgnoreVisible($this->ignoreVisible());
        return $output->render($this->_getComponent($componentId));
    }
}
