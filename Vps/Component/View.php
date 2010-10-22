<?php
/**
 * View, die zum Komponenten-Rendern verwendet wird
 */
class Vps_Component_View extends Vps_View
{
    private $_masterComponents;
    private $_componentMasterTemplates = array();
    private $_plugins = array();
    private $_isRenderMaster = false;
    private $_renderComponent;

    public function init()
    {
        parent::init();
        $this->addHelperPath(VPS_PATH . '/Vps/Component/View/Helper', 'Vps_Component_View_Helper');
    }

    /**
     * Gibt die Plugins für die Komponente genau einmal zurück
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

    /**
     * Gibt Parent-Komponenten, die ein Master-Template haben, zurück
     *
     * Im Template kann ein $this->component(...)-Aufruf einmal ein Master rendern
     * und beim nächsten Mal die Komponente selbst. Hier werden alle Master der parents
     * geholt und jedes Mal wenn beim einem $this->component() diese Methode von
     * Vps_Component_View_Helper_Component aufgerufen wird, wird die nächste
     * Master-Komponente zurückgegeben.
     *
     * @param Vps_Component_Data|null Komponente, die ein Master-Template hat
     */
    public function getNextParentMasterComponent($component)
    {
        if (!$this->_isRenderMaster) return null;
        if (is_null($this->_masterComponents)) {
            $this->_masterComponents = array();
            // beim parent anfangen
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

    /**
     * Gibt das Master-Template für die übergebene Komponente zurück
     *
     * Auch hier wird beim ersten Aufruf das Master-Template zurückgegeben und
     * beim nächsten Aufruf nichts mehr, da der Helper nicht weiß, ob er
     * zum ersten oder zweiten Mal aufgerufen wurde.
     *
     * @param string|null
     */
    public function getCurrentComponentMasterTemplate($component)
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

    public function setIsRenderMaster($isRenderMaster)
    {
        $this->_isRenderMaster = $isRenderMaster;
    }

    public function getRenderComponent()
    {
        return $this->_renderComponent;
    }

    public function setRenderComponent($renderComponent)
    {
        $this->_renderComponent = $renderComponent;
    }
}