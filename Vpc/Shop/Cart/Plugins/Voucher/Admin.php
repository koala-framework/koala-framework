<?php
class Vpc_Shop_Cart_Plugins_Voucher_Admin extends Vps_Component_Abstract_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        if (!$acl->has('vpc_shop')) {
            $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_shop',
                    array('text'=>trlVps('Shop'), 'icon'=>'cart.png')), 'vps_component_root');
        }
            $acl->add(new Vps_Acl_Resource_ComponentClass_MenuUrl($this->_class,
                    array('text'=>trlVps('Vouchers'), 'icon'=>'application_view_list.png'),
                    $this->getControllerUrl('Vouchers')), 'vpc_shop');
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['grid'] = array(
            'title' => trlVps('Vouchers'),
            'vouchersControllerUrl' => $this->getControllerUrl('Vouchers'),
            'voucherControllerUrl' => $this->getControllerUrl('Voucher'),
            'voucherHistoryControllerUrl' => $this->getControllerUrl('VoucherHistory'),
            'xtype' => 'vpc.shop.cart.plugins.voucher'
        );
        return $ret;
    }

}
