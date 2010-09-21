<?php
class Vpc_Shop_VoucherProduct_AddToCart_Form extends Vpc_Shop_AddToCartAbstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $values = array();
        for($i=1;$i<=10;$i++) {
            $values[$i*10] = Vps_View_Helper_Money::money($i*10);
        }
        $this->add(new Vps_Form_Field_NumberField('amount', trlcVps('Amount of Money', 'Amount')))
            ->setAllowNegative(false)
            ->setWidth(50)
            ->setValues($values)
            ->setAllowBlank(false)
            ->setComment('EUR');
    }
}
