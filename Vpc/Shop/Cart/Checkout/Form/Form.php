<?php
class Vpc_Shop_Cart_Checkout_Form_Form extends Vps_Form
{
    protected $_modelName = 'Vpc_Shop_Cart_Orders';

    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextField('firstname', trlVps('First name')));
        $this->add(new Vps_Form_Field_TextField('lastname', trlVps('Last name')));
        $this->add(new Vps_Form_Field_TextField('street', trlVps('Street')));
        $this->add(new Vps_Form_Field_TextField('zip', trlVps('ZIP')));
        $this->add(new Vps_Form_Field_TextField('city', trlVps('City')));
        $this->add(new Vps_Form_Field_SelectCountry('country', trlVps('Country')));
        $this->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
            ->setVType('email');
        $this->add(new Vps_Form_Field_TextField('phone', trlVps('Phone')));
        /*
        $this->add(new Vps_Form_Field_Radio('payment', trlVps('Payment')))
            ->setValues(array(
                'prepayment'=>trlVps('Prepayment'),
                'paypal'=>trlVps('Paypal'),
            ));
        */
    }
}
