<?php
class Vpc_Shop_Cart_Checkout_Form_Form extends Vpc_Abstract_Form
{
    protected $_modelName = 'Vpc_Shop_Cart_Orders';

    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_Panel('intro_text'))
            ->setHtml('<h1>'.trlVps('Please enter your address').'</h1>')
            ->setHideFieldInBackend(true);
        $this->add(new Vps_Form_Field_Radio('sex', trlcVps('sex', 'Title')))
            ->setValues(array(
                'male'   => trlVps('Mr.'),
                'female' => trlVps('Ms.')
            ))
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
        $this->add(new Vps_Form_Field_TextField('firstname', trlVps('First name')))
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('lastname', trlVps('Last name')))
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('street', trlVps('Street')))
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('zip', trlVps('ZIP')))
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('city', trlVps('City')))
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_SelectCountry('country', trlVps('Country')))
            ->setDefaultValue('AT')
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
            ->setVtype('email')
            ->setAllowBlank(false);
        $this->add(new Vps_Form_Field_TextField('phone', trlVps('Phone')));

        $this->add(new Vps_Form_Field_Panel('payment_text'))
            ->setHtml('<p class="formText">'.trlVps('What type of payment do you wish?').'</p>')
            ->setHideFieldInBackend(true);
        $this->add(new Vps_Form_Field_Radio('payment', trlVps('Payment')))
            ->setHideFieldInBackend(true)
            ->setAllowBlank(false);

        $this->add(new Vps_Form_Field_TextArea('comment', trlVps('Other comments, questions or suggestions')))
            ->setHeight(80)
            ->setWidth(200);
    }

    public function setPayments($payments)
    {
        $this->fields['payment']->setValues($payments);
        return $this;
    }
}
