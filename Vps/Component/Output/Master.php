<?php
class Vps_Component_Output_Master extends Vps_Component_Output_NoCache
{
    protected $_masterTemplates = array();

    public function render($component)
    {
        $c = $component;
        if ($component->componentId != Vps_Component_Data_Root::getInstance()->componentId) {
            $component = $component->parent;
        }
        while ($component) {
            $master = Vpc_Abstract::getTemplateFile($component->componentClass, 'Master');
            if ($master) {
                $this->_masterTemplates[] = array(
                    'component' => $c,
                    'template' => $master,
                    'masterComponent' => $component
                );
            }
            $component = $component->parent;
        }
        return $this->_render($c->componentId, $c->componentClass, true);
    }

    protected function _parseTemplate($ret)
    {
        if (isset($this->_masterTemplates[0])) {
            $componentClass = $this->_masterTemplates[0]['component']->componentClass;
            $componentId = $this->_masterTemplates[0]['component']->componentId;
            preg_match_all("/{nocache: $componentClass $componentId ?([^}]*)}/", $ret, $matches);
            foreach ($matches[0] as $key => $search) {
                $plugins = $matches[1][$key] ? explode(' ', trim($matches[1][$key])) : array();
                $replace = $this->_processComponent($componentId, $componentClass, false, $plugins);
                $ret = str_replace($search, $replace, $ret);
            }
        }
        return $ret;
    }

    protected function _renderContent($componentId, $componentClass, $masterTemplate)
    {
        // So lange die Master-Templates rendern, bis sie leer sind
        $component = $this->_getComponent($componentId);
        if (empty($this->_masterTemplates)) {
            return parent::_renderContent($componentId, $componentClass, false);
        } else {
            $masterTemplate = array_pop($this->_masterTemplates);
            $template = $masterTemplate['template'];
            $templateVars = array();
            $templateVars['component'] = $component;
            $templateVars['data'] = $component;
            $templateVars['boxes'] = array();
            $templateVars['cssClass'] = Vpc_Abstract::getCssClass($masterTemplate['masterComponent']->componentClass);
            foreach ($component->getChildBoxes() as $box) {
                $templateVars['boxes'][$box->box] = $box;
            }
            return $this->_renderView($template, $templateVars);
        }
    }
}
