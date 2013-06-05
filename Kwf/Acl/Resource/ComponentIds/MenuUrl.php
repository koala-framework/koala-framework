<?php
class Kwf_Acl_Resource_ComponentIds_MenuUrl extends Kwf_Acl_Resource_MenuDropdown
    implements Kwf_Acl_Resource_ComponentId_Interface, Kwf_Acl_Resource_HasChildResources_Interface
{
    protected $_componentClasses;
    protected $_menuUrl;

    public function __construct($resourceId, $componentClasses, $menuConfig = null, $menuUrl = null)
    {
        if (is_string($componentClasses)) $componentClasses = array($componentClasses);
        $this->_componentClasses = $componentClasses;
        $this->_menuUrl = $menuUrl;
        parent::__construct($resourceId, $menuConfig);
    }

    public function getChildResources()
    {
        $resources = array();
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsBySameClass(
            $this->_componentClasses, array('ignoreVisible' => true)
        );
        foreach($components as $component) {
            $resources[] = new Kwf_Acl_Resource_Component_MenuUrl(
                $this->_resourceId . '_' . $component->componentId,
                array('text' => $this->_menuConfig['text'] . ' ('.$component->name.')', 'icon' => $this->_menuConfig['icon']),
                $this->_menuUrl . '?componentId=' . $component->componentId,
                $component
            );
        }
        return $resources;
    }
}
