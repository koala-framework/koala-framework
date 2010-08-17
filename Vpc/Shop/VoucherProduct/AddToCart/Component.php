<?php
class Vpc_Shop_VoucherProduct_AddToCart_Component extends Vpc_Shop_AddToCartAbstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getPrice(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return $orderProduct->amount;
    }

    public function getAmount(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return 1;
    }

    public function getAdditionalOrderData(Vpc_Shop_Cart_OrderProduct $row)
    {
        $ret = parent::getAdditionalOrderData($row);
        $ret[] = array(
            'class' => 'amount',
            'name' => trlcVps('Amount of Money', 'Amount'),
            'value' => Vps_View_Helper_Money::money($row->amount)
        );
        return $ret;
    }

    public function orderConfirmed(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        //gutschein erstellen
        $row = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Plugins_Voucher_Vouchers')->createRow();
        $row->amount = $orderProduct->amount;
        $row->date = date('Y-m-d H:i:s');
        $row->comment = trlVps('Order').' '.$orderProduct->getParentRow('Order')->order_number;
        $row->save();
    }
}
