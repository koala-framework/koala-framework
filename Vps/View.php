<?php
class Vps_View extends Zend_View
{
    private $_masterComponents = null;
    private $_componentMasterTemplates = array();
    private $_renderMaster = false;
    private $_plugins = array();
    private $_isRenderMaster = false;
    private $_renderComponent;

    public function getPlugins($component)
    {
        $ret = array();
        $componentId = $component->componentId;

        // Keine Plugins bei Startkomponente außer es ist die root
        if ($this->_renderComponent &&
            $this->_renderComponent->componentId == $componentId &&
            $componentId != Vps_Component_Data_Root::getInstance()->componentId
        ) return $ret;

        if (!isset($this->_plugins[$componentId])) {
            $this->_plugins[$componentId] = $component->getPlugins();
        }

        if (count($this->_plugins[$componentId])) {
            $ret = $this->_plugins[$componentId];
            $this->_plugins[$componentId] = array();
        }
        return $ret;
    }

    public function getMasterComponent($component)
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

    public function getComponentMasterTemplate($component, $renderMaster = true)
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

    public function formatOutputConfig($outputConfig, $component)
    {
        // Master
        if ($outputConfig['type'] == 'component' && $this->_isRenderMaster) {
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
            $componentMasterTemplate = $this->_getComponentMasterTemplate($component, $this->_isRenderMaster);
            if ($componentMasterTemplate) {
                $outputConfig['config'] = array($componentMasterTemplate);
                $outputConfig['type'] = 'master';
            }
        }
        return $outputConfig;
    }

    public function setIsRenderMaster($isRenderMaster)
    {
        $this->_isRenderMaster = $isRenderMaster;
    }

    public function setRenderComponent($renderComponent)
    {
        $this->_renderComponent = $renderComponent;
    }

    public function getIsRenderMaster()
    {
        return $this->_isRenderMaster;
    }

    public function getRenderComponent()
    {
        return $this->_renderComponent;
    }














    protected $_masterTemplate = null;
    protected $_renderer = null;

    public function init()
    {
        // je weiter unten, desto wichtiger ist der pfad
        $this->addScriptPath(VPS_PATH); // für tests, damit man eigene templates wo ablegen kann für Vps_Mail_Template ohne komponente
        $this->addScriptPath('');
        $this->addScriptPath(VPS_PATH . '/views');
        $this->addScriptPath('application/views');
        $this->addHelperPath(VPS_PATH . '/Vps/View/Helper', 'Vps_View_Helper');
    }

    public function render($name)
    {
        if (!is_null($this->_masterTemplate)) {
            //TODO: partial von Zend_View verwenden
            $this->renderedTemplate = parent::render($name);
            $name = $this->getMasterTemplate();
        }
        return parent::render($name);
    }

    public function setMasterTemplate($tpl)
    {
        $this->_masterTemplate = $tpl;
    }

    public function getMasterTemplate()
    {
        return $this->_masterTemplate;
    }

    public function setRenderer($renderer)
    {
        $this->_renderer = $renderer;
    }

    public function getRenderer()
    {
        return $this->_renderer;
    }
}
