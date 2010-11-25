<?php
class Vpc_Shop_VoucherProduct_AddToCart_OrderProductData extends Vpc_Shop_AddToCartAbstract_OrderProductData
{
    public function getPrice(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return $orderProduct->amount;
    }

    public function getAmount(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return 1;
    }

    public function getAdditionalOrderData(Vpc_Shop_Cart_OrderProduct $orderProduct)
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

    public function orderConfirmed(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        //gutschein erstellen
        $row = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Plugins_Voucher_Vouchers')->createRow();
        $row->amount = $orderProduct->amount;
        $row->date = date('Y-m-d H:i:s');
        $row->comment = trlVps('Order').' '.$orderProduct->getParentRow('Order')->order_number;
        $row->save();
    }

    public function getProductText(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return trlVps('Voucher');
    }

    public function alterBackendOrderForm(Vps_Form $form)
    {
        $form->prepend(new Vps_Form_Field_NumberField('amount', trlcVps('Amount of Money', 'Amount')))
            ->setAllowNegative(false)
            ->setWidth(50)
            ->setAllowBlank(false)
            ->setComment('EUR');
    }
}
