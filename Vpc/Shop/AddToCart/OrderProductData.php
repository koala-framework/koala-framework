<?php
class Vpc_Shop_AddToCart_OrderProductData extends Vpc_Shop_AddToCartAbstract_OrderProductData
{
    public function getPrice(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return $orderProduct->getParentRow('ProductPrice')->price * $orderProduct->amount;
    }

    public function getAmount(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return $orderProduct->amount;
    }

    public function getProductText(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        $product = $orderProduct->getParentRow('ProductPrice')->getParentRow('Product');
        return $product->__toString();
    }

    public function getAdditionalOrderData(Vpc_Shop_Cart_OrderProduct $row)
    {
        $ret = parent::getAdditionalOrderData($row);
        $ret[] = array(
            'class' => 'amount',
            'name' => trlVps('Amount'),
            'value' => $row->amount
        );
        return $ret;
    }

    public function alterBackendOrderForm(Vpc_Shop_AddToCartAbstract_FrontendForm $form)
    {
        $m = Vps_Model_Abstract::getInstance('Vpc_Shop_Products');
        $s = $m->select();
        $s->whereEquals('visible', 1);
        $s->order('pos');
        $data = array();
        foreach ($m->getRows($s) as $product) {
            $data[] = array(
                $product->current_price_id,
                $product->__toString().' ('.$product->current_price.' €)'
            );
        }
        $form->prepend(new Vps_Form_Field_Select('shop_product_price_id', trlVps('Product')))
            ->setValues($data);
    }
}
