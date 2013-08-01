<?php
class Kwf_Component_Generator_Plugin_Tags_Trl_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        $name = Kwc_Abstract::getSetting($this->_class, 'componentName');
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        $acl->add(new Kwf_Acl_Resource_ComponentClass_MenuUrl(
            $this->_class,
            array('text' => $name, 'icon'=> $icon),
            Kwc_Admin::getInstance($this->_class)->getControllerUrl()
        ), 'kwf_component_root');
    }
}
