<?php
class Kwc_Shop_Cart_Plugins_Voucher_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        if (!$acl->has('kwc_shop')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_shop',
                    array('text'=>trlKwfStatic('Shop'), 'icon'=>'cart.png')), 'kwf_component_root');
        }
            $acl->add(new Kwf_Acl_Resource_ComponentClass_MenuUrl($this->_class,
                    array('text'=>trlKwfStatic('Vouchers'), 'icon'=>'application_view_list.png'),
                    Kwc_Admin::getInstance($this->_class)->getControllerUrl('Vouchers')), 'kwc_shop');
    }
}
