<?php
class Kwc_Shop_Cart_Checkout_Form_FrontendForm extends Kwf_Form
{
    protected $_modelName = 'Kwc_Shop_Cart_Orders';
    private $_payments;

    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_Panel('intro_text'))
            ->setHtml('<h1>'.trlKwf('Please enter your address').'</h1>')
            ->setHideFieldInBackend(true);
        $this->add(new Kwf_Form_Field_Radio('sex', trlcKwf('sex', 'Title')))
            ->setValues(array(
                'male'   => trlKwf('Mr.'),
                'female' => trlKwf('Ms.')
            ))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')));
        $this->add(new Kwf_Form_Field_TextField('firstname', trlKwf('First name')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('lastname', trlKwf('Last name')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('street', trlKwf('Street')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('zip', trlKwf('ZIP')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('city', trlKwf('City')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_SelectCountry('country', trlKwf('Country')))
            ->setDefaultValue('AT')
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('email', trlKwf('E-Mail')))
            ->setVtype('email')
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('phone', trlKwf('Phone')));

        $this->add(new Kwf_Form_Field_Panel('payment_text'))
            ->setHtml('<p class="formText">'.trlKwf('What type of payment do you wish?').'</p>')
            ->setHideFieldWhenOnePayment(true)
            ->setHideFieldInBackend(true);
        $this->add(new Kwf_Form_Field_Radio('payment', trlKwf('Payment')))
            ->setHideFieldInBackend(true)
            ->setHideFieldWhenOnePayment(true)
            ->setAllowBlank(false);

        $this->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Other comments, questions or suggestions')))
            ->setHeight(80)
            ->setWidth(200);
    }

    public function setPayments($payments)
    {
        $this->_payments = $payments;
        $this->fields['payment']->setValues($payments);
        if (count($payments) == 1) {
            foreach ($this->fields as $f) {
                if ($f->getHideFieldWhenOnePayment()) {
                    $this->fields->remove($f);
                }
            }
        }
        return $this;
    }


    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        if (count($this->_payments) == 1) {
            $payments = array_keys($this->_payments);
            $row->payment = $payments[0];
        }
    }
}
