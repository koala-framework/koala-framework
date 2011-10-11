<?php
class Kwf_Component_Abstract_MenuConfig_SameClass extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $name = Kwc_Abstract::getSetting($this->_class, 'componentName');
        if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        if (count($components) > 1) {
            $dropdownName = 'kwc_'.$this->_class;
            if (!$acl->has($dropdownName)) {
                $acl->add(
                    new Kwf_Acl_Resource_MenuDropdown(
                        $dropdownName, array('text'=>$name, 'icon'=>$icon)
                    ), 'kwf_component_root'
                );
            }
            foreach ($components as $c) {
                $t = $c->getTitle();
                if (!$t) $t = $name;
                $acl->add(
                    new Kwf_Acl_Resource_Component_MenuUrl(
                        $c, array('text'=>$t, 'icon'=>$icon)
                    ), $dropdownName
                );
            }
        } else if (count($components) == 1) {
            $c = $components[0];
            $acl->add(
                new Kwf_Acl_Resource_Component_MenuUrl(
                    $c, array('text'=>$name, 'icon'=>$icon)
                ), 'kwf_component_root'
            );
        }
    }
}
