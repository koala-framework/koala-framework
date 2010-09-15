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
}
