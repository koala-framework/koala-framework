<?php
class Vpc_Shop_Cart_Form extends Vps_Form
{
    protected $_modelName = 'Vpc_Shop_Cart_Orders';
    
    protected function _init()
    {
        parent::_init();
        $mf = $this->add(new Vps_Form_Field_MultiFields('Vpc_Shop_Cart_OrderProducts'));

        //TODO: reuse addToCart-Form
        $mf->fields->add(new Vps_Form_Field_NumberField('amount', trlVps('Amount')));
        $mf->fields->add(new Vps_Form_Field_NumberField('size', trlVps('Size')));
    }
}
