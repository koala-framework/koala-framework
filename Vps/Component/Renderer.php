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

    /**
     * Gibt die Plugins für die Komponente *genau einmal* zurück
     *
     * Der Helper fragt einmal beim Master (falls es einen gibt) und einmal
     * bei der Komponente
     */
    public function getPlugins(Vps_Component_Data $component)
    {
        $ret = array();
        $componentId = $component->componentId;

        if (!isset($this->_plugins[$componentId])) {
            $this->_plugins[$componentId] = $component->getPlugins();
        }

        if (count($this->_plugins[$componentId])) {
            $ret = $this->_plugins[$componentId];
            $this->_plugins[$componentId] = array();
        }
        return $ret;
    }
}
