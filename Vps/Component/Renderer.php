<?php
class Vps_Component_Renderer extends Vps_Component_Renderer_Abstract
{
    private $_isRenderMaster = false;
    private $_masterComponents;
    private $_componentMasterTemplates = array();
    private $_plugins = array();

    public function renderMaster($component)
    {
        return $this->renderComponent($component, true);
    }

    public function renderComponent($component, $renderMaster = false)
    {
        $this->_plugins = array();
        $this->_componentMasterTemplates = array();
        $this->_masterComponents = null;
        $this->_isRenderMaster = $renderMaster;
        return parent::renderComponent($component);
    }
}
