<?php
class Kwc_Shop_Cart_Checkout_Form_FrontendForm extends Kwf_Form
{
    private $_payments;

    protected function _initFields()
    {
        parent::_initFields();

        $this->setCartEmptyMessage(trlKwfStatic("Can't submit order because the cart is empty."));

        $this->add(new Kwf_Form_Field_Panel('intro_text'))
            ->setHtml('<h1>'.trlKwfStatic('Please enter your address').'</h1>')
            ->setHideFieldInBackend(true);
        $this->add(new Kwf_Form_Field_Radio('sex', trlcKwfStatic('sex', 'Title')))
            ->setValues(array(
                'male'   => trlKwfStatic('Mr.'),
                'female' => trlKwfStatic('Ms.')
            ))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('title', trlKwfStatic('Title')));
        $this->add(new Kwf_Form_Field_TextField('firstname', trlKwfStatic('First name')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('lastname', trlKwfStatic('Last name')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('street', trlKwfStatic('Street')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('zip', trlKwfStatic('ZIP')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('city', trlKwfStatic('City')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_SelectCountry('country', trlKwfStatic('Country')))
            ->setDefaultValue('AT')
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('email', trlKwfStatic('E-Mail')))
            ->setVtype('email')
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('phone', trlKwfStatic('Phone')));

        $this->add(new Kwf_Form_Field_Panel('payment_text'))
            ->setHtml('<p class="formText">'.trlKwfStatic('What type of payment do you wish?').'</p>')
            ->setHideFieldWhenOnePayment(true)
            ->setHideFieldInBackend(true);
        $this->add(new Kwf_Form_Field_Radio('payment', trlKwfStatic('Payment')))
            ->setHideFieldInBackend(true)
            ->setHideFieldWhenOnePayment(true)
            ->setAllowBlank(false);

        $this->add(new Kwf_Form_Field_TextArea('comment', trlKwfStatic('Other comments, questions or suggestions')));
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'cartEmptyMessage';
        return $ret;
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


    public function validate($parentRow, array $postData = array())
    {
        $ret = parent::validate($parentRow, $postData);
        if ($this->getAllowEmptyCart() !== true) {
            $row = $this->getRow($parentRow);
            if (!count($row->getChildRows('Products'))) {
                $ret[] = array(
                    'field' => null,
                    'messages' => array(
                        $this->getCartEmptyMessage()
                    )
                );
            }
        }
        return $ret;
    }
}
