<?php
class Vpc_Shop_AddToCart_FrontendForm extends Vpc_Shop_AddToCartAbstract_FrontendForm
{
    protected $_product;
    protected function _init()
    {
        parent::_init();
        $this->add(new Vps_Form_Field_Select('amount', trlVps('Amount')))
            ->setAllowBlank(false)
            ->setValues(array(
                1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 6=>6, 7=>7, 8=>8, 9=>9, 10=>10
            ));
    }

    public function setProduct(Vpc_Shop_Product $product)
    {
        $this->_product = $product;
    }

    public function setProductId($productId)
    {
        if (!Vpc_Shop_Cart_Orders::getCartOrderId()) {
            $this->setId(null);
        } else {
            $where = array(
                'shop_product_id = ?' => $productId,
                'shop_order_id = ?' => Vpc_Shop_Cart_Orders::getCartOrderId()
            );
            //TODO: verbessern (speed?), nicht sinnlos row holen und nur id übergebn
            $row = $this->getModel()->fetchAll($where)->current();
            if ($row) {
                $this->setId($row->id);
            } else {
                $this->setId(null);
            }
        }
    }
}
