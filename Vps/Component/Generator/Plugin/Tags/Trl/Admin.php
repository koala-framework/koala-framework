<?php
class Vps_Component_Generator_Plugin_Tags_Trl_Admin extends Vps_Component_Abstract_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $name = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        $acl->add(new Vps_Acl_Resource_ComponentClass_MenuUrl(
            $this->_class,
            array('text' => $name, 'icon'=> $icon),
            $this->getControllerUrl()
        ), 'vps_component_root');
    }
}
