<?php
class Kwf_Component_Generator_Plugin_Tags_Trl_Admin extends Kwf_Component_Abstract_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        $name = Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($this->_class, 'componentName'));
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        $acl->add(new Kwf_Acl_Resource_ComponentClass_MenuUrl(
            $this->_class,
            array('text' => $name, 'icon'=> $icon),
            $this->getControllerUrl()
        ), 'kwf_component_root');
    }
}
