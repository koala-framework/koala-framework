<?php
class Vpc_Shop_VoucherProduct_AddToCart_FrontendForm extends Vpc_Shop_AddToCartAbstract_FrontendForm
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_NumberField('amount', trlcVps('Amount of Money', 'Amount')))
            ->setAllowNegative(false)
            ->setWidth(50)
            ->setAllowBlank(false)
            ->setComment('EUR');
    }
}
