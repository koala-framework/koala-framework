<?php
class Vpc_Shop_AddToCart_Component extends Vpc_Shop_AddToCartAbstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setProduct($this->_getProduct());
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->shop_product_price_id = $this->_getProduct()->current_price_id;
    }

    protected function _getProduct()
    {
        if (isset($this->getData()->row)) {
            //wenn direkt von table generator erstellt
            return $this->getData()->row;
        }
        return $this->getData()->parent->row;
    }

    public function getPrice(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return $orderProduct->getParentRow('ProductPrice')->price * $orderProduct->amount;
    }

    public function getAmount(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return $orderProduct->amount;
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
}
