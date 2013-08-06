<?php
class Kwf_Component_Abstract_MenuConfig_Trl_SameClass extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    //we need to modify resources from master, so we have to be added to acl *after* master
    public function getPriority()
    {
        return 5;
    }

    public function addResources(Kwf_Acl $acl)
    {
        $masterCls = $this->_getSetting('masterComponentClass');
        if (Kwc_Abstract::hasSetting($this->_class, 'componentNameShort')) {
            $name = Kwc_Abstract::getSetting($this->_class, 'componentNameShort');
        } else {
            $name = Kwc_Abstract::getSetting($this->_class, 'componentName');
        }
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');

        // **** create dropdown
        $dropdownName = 'kwc_'.$masterCls;
        if (!$acl->has($dropdownName)) {
            $acl->addResource(
                new Kwf_Acl_Resource_MenuDropdown(
                    $dropdownName, array('text'=>$name, 'icon'=>$icon)
                ), 'kwf_component_root'
            );
        }

        // **** modify master component
        $masterComponents = Kwf_Component_Data_Root::getInstance()
            ->getComponentsBySameClass($masterCls, array('ignoreVisible'=>true));

        //add language name to menu text
        foreach ($masterComponents as $c) {
            $resource = $acl->get('kwc_'.$c->dbId);
            $mc = $resource->getMenuConfig();
            $mc['text'] .= ' ('.$c->getBaseProperty('language').')';
            $resource->setMenuConfig($mc);
        }
        if (count($masterComponents) > 1) {
            //already in dropdown
        } else if (count($masterComponents) == 1) {
            //just one, move into dropdown
            $c = $masterComponents[0];
            $resource = $acl->get('kwc_'.$c->dbId);
            $acl->remove($resource);
            $acl->addResource($resource, $dropdownName);
        }

        // *** add own
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        foreach ($components as $c) {
            $t = $c->getTitle();
            if (!$t) $t = $name;
            if ($domain = $c->getParentByClass('Kwc_Root_DomainRoot_Domain_Component')) {
                $t .= " ($domain->name)";
            }
            $t .= ' ('.$c->getBaseProperty('language').')';
            $acl->add(
                new Kwf_Acl_Resource_Component_MenuUrl(
                    $c, array('text'=>$t, 'icon'=>$icon)
                ), $dropdownName
            );
        }
    }
}
