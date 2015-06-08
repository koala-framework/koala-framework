<?php
class Kwc_Shop_AddToCart_FrontendForm extends Kwc_Shop_AddToCartAbstract_FrontendForm
{
    protected $_product;

    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_Select('amount', trlKwfStatic('Amount')))
            ->setAllowBlank(false)
            ->setValues($this->_getAmountValues())
            ->setEditable(true);
    }

    protected function _getAmountValues($count = 10)
    {
        $ret = array();
        for ($x = 1; $x <= $count; $x++) {
            $ret[$x] = $x;
        }
        return $ret;
    }

    public function setProduct(Kwc_Shop_Product $product)
    {
        $this->_product = $product;
    }
}
