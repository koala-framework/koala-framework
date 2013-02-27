<?php
class Kwc_Shop_VoucherProduct_AddToCart_FrontendForm extends Kwc_Shop_AddToCartAbstract_FrontendForm
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_NumberField('value', trlcKwfStatic('Amount of Money', 'Amount')))
            ->setAllowNegative(false)
            ->setWidth(50)
            ->setAllowBlank(false)
            ->setComment('EUR');
    }
}
