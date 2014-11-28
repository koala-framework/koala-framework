<?php
class Kwc_Form_Dynamic_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));

        if (Kwc_Abstract::hasSetting($this->_class, 'componentNameShort')) {
            $name = Kwc_Abstract::getSetting($this->_class, 'componentNameShort');
        } else {
            $name = Kwc_Abstract::getSetting($this->_class, 'componentName');
        }
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        foreach ($components as $c) {
            $t = $c->getTitle();
            if (!$t && $c->getPage()) $t = $c->getPage()->name;
            if ($domain = $c->getParentByClass('Kwc_Root_DomainRoot_Domain_Component')) {
                $t .= " - $domain->name";
            }
            $t = $name .' ('.$t.')';
            $menuUrl = Kwc_Admin::getInstance($c->componentClass)
                ->getControllerUrl('Enquiries') . '?componentId=' . $c->dbId;
            $acl->addResource(
                new Kwf_Acl_Resource_Component_MenuUrl(
                    $c, array('text'=>$t, 'icon'=>$icon), $menuUrl
                ), 'kwf_enquiries_dropdown'
            );
        }
    }

    public function getEventsClass()
    {
        return 'Kwf_Component_Abstract_MenuConfig_SameClass_Events';
    }
}
