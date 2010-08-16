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

    public function getAdditionalOrderData(Vpc_Shop_Cart_OrderProduct $row)
    {
        $ret = parent::getAdditionalOrderData($row);
        $ret[] = array(
            'class' => 'amount',
            'name' => trlcVps('Amount of Money', 'Amount'),
            'value' => $row->amount.' €'
        );
        return $ret;
    }
}
