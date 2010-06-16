<?php
class Vps_Component_Output
{
    private $_ignoreVisible = false;
    private $_enableCache = false;

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

    private function _getMasterTemplates($component)
    {
        $ret = array();
        $renderComponent = $component;
        while ($component) {
            $master = Vpc_Abstract::getTemplateFile($component->componentClass, 'Master');
            if ($master) $ret[] = $master;
            if ($renderComponent->componentId == $component->componentId &&
                !$component->isPage)
            {
                break; // Auf der Page alle Master holen, sonst nur eigenes Master
            }
            $component = $component->parent;
        }
        return $ret;
    }

    public function render($component)
    {
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
                    if (!isset($masterTemplates[$componentId])) {
                        $masterTemplates[$componentId] = $this->_getMasterTemplates($component);
                    }
                    $c = $component;
                    while ($c) {
                        if ($masterTemplates[$componentId]) {
                            $masterTemplate = array_pop($masterTemplates[$componentId]);
                            $config = array(array_pop($masterTemplates[$componentId]));
                            $type = 'master';
                            break;
                        }
                        $c = $c->parent;
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