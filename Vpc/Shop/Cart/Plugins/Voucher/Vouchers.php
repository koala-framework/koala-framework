<?php
class Vpc_Shop_Cart_Plugins_Voucher_Vouchers extends Vps_Model_Db
{
    protected $_table = 'vpc_shop_vouchers';
    protected $_dependentModels = array(
        'history' => 'Vpc_Shop_Cart_Plugins_Voucher_VoucherHistory'
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['used_amount'] = new Vps_Model_Select_Expr_Child_Sum('history', 'amount');

        $this->_filters['code'] = new Vps_Filter_Row_Random(8);
    }
}
