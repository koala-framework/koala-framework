<?php
class Vpc_Shop_Cart_Checkout_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        return array();
    }

    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_shop',
                    array('text'=>trlVps('Shop'), 'icon'=>'cart.png')), 'vps_component_root');
/*
            $acl->add(new Vps_Acl_Resource_ComponentClass_MenuUrl($this->_class,
                    array('text'=>trlVps('Orders'), 'icon'=>'application_view_list.png'),
                    $this->getControllerUrl('Orders')), 'vpc_shop');
*/
        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        foreach ($components as $c) {
            $acl->add(new Vps_Acl_Resource_Component_MenuUrl($c,
                    array('text'=>$c->getTitle(), 'icon'=>$icon),
                    Vpc_Admin::getInstance($c->componentClass)->getControllerUrl('Orders').'?componentId='.$c->dbId), 'vpc_shop');
        }
    }
}
