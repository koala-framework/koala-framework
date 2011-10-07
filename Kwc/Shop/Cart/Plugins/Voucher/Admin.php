<?php
class Kwc_Shop_Cart_Plugins_Voucher_Admin extends Kwf_Component_Abstract_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        if (!$acl->has('kwc_shop')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_shop',
                    array('text'=>trlKwf('Shop'), 'icon'=>'cart.png')), 'kwf_component_root');
        }
            $acl->add(new Kwf_Acl_Resource_ComponentClass_MenuUrl($this->_class,
                    array('text'=>trlKwf('Vouchers'), 'icon'=>'application_view_list.png'),
                    $this->getControllerUrl('Vouchers')), 'kwc_shop');
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['grid'] = array(
            'title' => trlKwf('Vouchers'),
            'vouchersControllerUrl' => $this->getControllerUrl('Vouchers'),
            'voucherControllerUrl' => $this->getControllerUrl('Voucher'),
            'voucherHistoryControllerUrl' => $this->getControllerUrl('VoucherHistory'),
            'xtype' => 'kwc.shop.cart.plugins.voucher'
        );
        return $ret;
    }

}
