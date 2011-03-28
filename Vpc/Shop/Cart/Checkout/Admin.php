<?php
class Vpc_Shop_Cart_Checkout_Admin extends Vpc_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        if (!$acl->has('vpc_shop')) {
            $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_shop',
                    array('text'=>trlVps('Shop'), 'icon'=>'cart.png')), 'vps_component_root');
        }
        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        foreach ($components as $c) {
            $acl->add(new Vps_Acl_Resource_Component_MenuUrl($c,
                    array('text'=>trlVps('Orders'), 'icon'=>$icon),
                    Vpc_Admin::getInstance($c->componentClass)->getControllerUrl('Orders').'?componentId='.$c->dbId), 'vpc_shop');
        }
    }
}
