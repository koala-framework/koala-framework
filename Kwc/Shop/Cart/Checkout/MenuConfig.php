<?php
class Kwc_Shop_Cart_Checkout_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        if (!$acl->has('kwc_shop')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_shop',
                    array('text'=>trlKwf('Shop'), 'icon'=>'cart.png')), 'kwf_component_root');
        }
        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        foreach ($components as $c) {
            $text = trlKwfStatic('Orders');
            if (count($components) > 1) {
                if ($domain = $c->getParentByClass('Kwc_Root_DomainRoot_Domain_Component')) {
                    $text .= " ($domain->name)";
                }
            }
            $acl->add(new Kwf_Acl_Resource_Component_MenuUrl($c,
                    array('text'=>$text, 'icon'=>$icon),
                    Kwc_Admin::getInstance($c->componentClass)->getControllerUrl('Orders').'?componentId='.$c->dbId), 'kwc_shop');
        }
    }

    public function getEventsClass()
    {
        return 'Kwf_Component_Abstract_MenuConfig_SameClass_Events';
    }
}
