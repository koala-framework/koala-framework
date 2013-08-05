<?php
class Kwf_Component_Abstract_MenuConfig_SameClass extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    protected function _getParentResource(Kwf_Acl $acl)
    {
        return 'kwf_component_root';
    }

    public function addResources(Kwf_Acl $acl)
    {
        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        if (Kwc_Abstract::hasSetting($this->_class, 'componentNameShort')) {
            $name = Kwc_Abstract::getSetting($this->_class, 'componentNameShort');
        } else {
            $name = Kwc_Abstract::getSetting($this->_class, 'componentName');
        }
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        if (count($components) > 1) {
            $dropdownName = 'kwc_'.$this->_class;
            if (!$acl->has($dropdownName)) {
                $dropDown = new Kwf_Acl_Resource_MenuDropdown(
                        $dropdownName, array('text'=>$name, 'icon'=>$icon)
                    );
                $dropDown->setCollapseIfSingleChild(true);
                $acl->add($dropDown, $this->_getParentResource($acl));
            }
            foreach ($components as $c) {
                $t = $c->getTitle();
                if (!$t) $t = $name;
                if ($domain = $c->getParentByClass('Kwc_Root_DomainRoot_Domain_Component')) {
                    $t .= " ($domain->name)";
                }
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
                ), $this->_getParentResource($acl)
            );
        }
    }

    public function getEventsClass()
    {
        return 'Kwf_Component_Abstract_MenuConfig_SameClass_Events';
    }
}
