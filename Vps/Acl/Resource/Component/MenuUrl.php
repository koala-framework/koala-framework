<?php
class Vps_Acl_Resource_Component_MenuUrl extends Vps_Acl_Resource_MenuUrl
    implements Vps_Acl_Resource_Component_Interface
{
    protected $_component;

    public function __construct(Vps_Component_Data $component, $menuConfig = null, $menuUrl = null)
    {
        $this->_component = $component;
        if (!$menuConfig) {
            $name = Vpc_Abstract::getSetting($component->componentClass, 'componentName');
            $icon = Vpc_Abstract::getSetting($component->componentClass, 'componentIcon');
            if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);
            $menuConfig = array('text'=>trlVps('Edit {0}', $name), 'icon'=>$icon);
        }
        if (!$menuUrl) {
            $menuUrl = Vpc_Admin::getInstance($component->componentClass)
                ->getControllerUrl() . '?componentId=' . $component->dbId;
        }
        parent::__construct('vpc_'.$component->dbId, $menuConfig, $menuUrl);
    }

    public function getComponent()
    {
        return $this->_component;
    }
}
