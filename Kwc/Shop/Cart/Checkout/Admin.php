<?php
class Kwc_Shop_Cart_Checkout_Admin extends Kwc_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        if (!$acl->has('kwc_shop')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_shop',
                    array('text'=>trlKwf('Shop'), 'icon'=>'cart.png')), 'kwf_component_root');
        }
        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        foreach ($components as $c) {
            $text = trlKwf('Orders');
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
}
