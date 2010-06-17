<?php
class Vps_Component_Output
{
    private $_ignoreVisible = false;
    private $_enableCache = false;
    private $_masterTemplates = array();

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

    private function _getMasterTemplate($component, $renderMaster = true)
    {
        $componentId = $component->componentId;
        if (!isset($this->_masterTemplates[$componentId])) {
            $templates = array();
            $renderComponent = $component;
            while ($component) {
                $master = Vpc_Abstract::getTemplateFile($component->componentClass, 'Master');
                if ($master) $templates[] = $master;
                if (!$renderMaster &&
                    $renderComponent->componentId == $component->componentId &&
                    !$component->isPage)
                {
                    break; // Auf der Page alle Master holen, sonst nur eigenes Master
                }
                $component = $component->parent;
            }
            $this->_masterTemplates[$componentId] = $templates;
        }

        if (count($this->_masterTemplates[$componentId])) {
            return array_pop($this->_masterTemplates[$componentId]);
        } else {
            return array();
        }
    }

    public function renderMaster($component)
    {
        return $this->render($component, true);
    }

    public function render($component, $renderMaster = false)
    {
        $this->_masterTemplates = array();
        $matches = array(
            array('{component}'),
            array('component'),
            array($component->componentId),
            array('')
        );
        $ret = '{component}';
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

                if ($type == 'component') {
                    $masterTemplate = $this->_getMasterTemplate($component, $renderMaster);
                    if ($masterTemplate) {
                        $config = array($masterTemplate);
                        $type = 'master';
                    }
                }

                $class = 'Vps_Component_Output_' . ucfirst($type);
                $output = new $class();
                $content = $output->render($component, $config);
                $ret = str_replace($search, $content, $ret);
            }
            preg_match_all('/{([^ }]+): ([^ }]+)([^}]*)}/', $ret, $matches);
        } while ($matches[0]);

        return $ret;
    }
}