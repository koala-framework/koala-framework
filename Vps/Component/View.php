<?php
class Vps_Component_View extends Vps_Component_View_Abstract
{
    private $_masterTemplates = null;
    private $_componentMasterTemplates = array();
    private $_renderMaster = false;
    private $_renderComponentId;

    protected function _getMasterTemplate($component)
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
        $this->_masterTemplates = null;
        $this->_componentMasterTemplates = array();
        $this->_renderMaster = $renderMaster;
        return parent::renderComponent($component);
    }

    protected function _formatOutputConfig($outputConfig, $component)
    {
        // Master
        if ($outputConfig['type'] == 'component' && $this->_renderMaster) {
            $masterTemplate = $this->_getMasterTemplate($component);
            if ($masterTemplate) {
                $outputConfig['config'] = array($masterTemplate);
                $outputConfig['type'] = 'master';
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
