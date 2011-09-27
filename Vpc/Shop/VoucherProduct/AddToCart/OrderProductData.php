<?php
class Vpc_Shop_VoucherProduct_AddToCart_OrderProductData extends Vpc_Shop_AddToCartAbstract_OrderProductData
{
    public function getPrice($orderProduct)
    {
        return $orderProduct->amount;
    }

    public function getAmount($orderProduct)
    {
        return 1;
    }

    public function getAdditionalOrderData($orderProduct)
    {
        $ret = parent::getAdditionalOrderData($orderProduct);
        /*
        $ret[] = array(
            'class' => 'amount',
            'name' => trlcVps('Amount of Money', 'Amount'),
            'value' => Vps_View_Helper_Money::money($orderProduct->amount)
        );
        */
        return $ret;
    }

    public function orderConfirmed($orderProduct)
    {
        //gutschein erstellen
        $row = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Plugins_Voucher_Vouchers')->createRow();
        $row->amount = $orderProduct->amount;
        $row->date = date('Y-m-d H:i:s');
        $row->comment = trlVps('Order').' '.$orderProduct->getParentRow('Order')->order_number;
        $row->save();
    }

    public function getProductText($orderProduct)
    {
        return trlVps('Voucher');
    }
}
