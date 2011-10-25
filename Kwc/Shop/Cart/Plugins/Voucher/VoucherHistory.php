<?php
class Kwc_Shop_Cart_Plugins_Voucher_VoucherHistory extends Kwf_Model_Db
{
    protected $_table = 'kwc_shop_voucher_history';

    protected $_referenceMap = array(
        'voucher' => array(
            'column' => 'voucher_id',
            'refModelClass' => 'Kwc_Shop_Cart_Plugins_Voucher_Vouchers'
        )
    );
}
