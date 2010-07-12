<?php
class Vps_Component_Renderer extends Vps_Component_Renderer_Abstract
{
    private $_masterComponents = null;
    private $_componentMasterTemplates = array();
    private $_renderMaster = false;
    private $_renderComponentId;

    protected function _getMasterComponent($component)
    {
        if (is_null($this->_masterComponents)) {
            $this->_masterComponents = array();
            if ($component->componentId != Vps_Component_Data_Root::getInstance()->componentId) {
                $component = $component->parent;
            }
            while ($component) {
                $master = Vpc_Abstract::getTemplateFile($component->componentClass, 'Master');
                if ($master) {
                    $component->masterTemplate = $master;
                    $this->_masterComponents[] = $component;
                }
                $component = $component->parent;
            }
        }

        $ret = null;
        if (count($this->_masterComponents)) $ret = array_pop($this->_masterComponents);
        return $ret;
    }

    protected function _getComponentMasterTemplate($component, $renderMaster = true)
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

    public function renderMaster($component)
    {
        return $this->renderComponent($component, true);
    }

    public function renderComponent($component, $renderMaster = false)
    {
        $this->_masterComponents = null;
        $this->_componentMasterTemplates = array();
        $this->_renderMaster = $renderMaster;
        return parent::renderComponent($component);
    }

    protected function _formatOutputConfig($outputConfig, $component)
    {
        // Master
        if ($outputConfig['type'] == 'component' && $this->_renderMaster) {
            $masterComponent = $this->_getMasterComponent($component);
            if ($masterComponent) {
                $outputConfig['config'] = array($masterComponent->masterTemplate, $masterComponent->componentId);
                $outputConfig['type'] = 'master';
                $outputConfig['value'] = $masterComponent->componentId;
            }
        }
        // Plugins
        if ($outputConfig['type'] == 'component') {
            $outputConfig['plugins'] = $this->_getPlugins($component);
        }
        // ComponentMaster
        if ($outputConfig['type'] == 'component') {
            $componentMasterTemplate = $this->_getComponentMasterTemplate($component, $this->_renderMaster);
            if ($componentMasterTemplate) {
                $outputConfig['config'] = array($componentMasterTemplate);
                $outputConfig['type'] = 'master';
            }
        }
        return $outputConfig;
    }
}
