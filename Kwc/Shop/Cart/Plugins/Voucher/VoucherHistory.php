<?php
class Vpc_Shop_Cart_Plugins_Voucher_VoucherHistory extends Vps_Model_Db
{
    protected $_table = 'vpc_shop_voucher_history';

    protected $_referenceMap = array(
        'voucher' => array(
            'column' => 'voucher_id',
            'refModelClass' => 'Vpc_Shop_Cart_Plugins_Voucher_Vouchers'
        )
    );
}
