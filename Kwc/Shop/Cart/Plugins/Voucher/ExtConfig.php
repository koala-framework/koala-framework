<?php
class Kwc_Shop_Cart_Plugins_Voucher_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
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
