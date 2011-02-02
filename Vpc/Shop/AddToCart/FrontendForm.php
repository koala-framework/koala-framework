<?php
class Vpc_Shop_AddToCart_FrontendForm extends Vpc_Shop_AddToCartAbstract_FrontendForm
{
    protected $_product;

    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_Select('amount', trlVps('Amount')))
            ->setAllowBlank(false)
            ->setValues($this->_getAmountValues());
    }

    protected function _getAmountValues($count = 10)
    {
        $ret = array();
        for ($x = 1; $x <= $count; $x++) {
            $ret[$x] = $x;
        }
        return $ret;
    }

    public function setProduct(Vpc_Shop_Product $product)
    {
        $this->_product = $product;
    }
}
