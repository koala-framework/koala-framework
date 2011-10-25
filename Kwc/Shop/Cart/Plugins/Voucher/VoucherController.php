<?php
class Kwc_Shop_Cart_Plugins_Voucher_VoucherController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwc_Shop_Cart_Plugins_Voucher_Vouchers';
    protected $_permissions = array('add', 'save');

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->add(new Kwf_Form_Field_ShowField('code', trlKwf('Code')));
        $this->_form->add(new Kwf_Form_Field_NumberField('amount', trlcKwf('Amount of Money', 'Amount')))
            ->setWidth(50)
            ->setComment('€');
        $this->_form->add(new Kwf_Form_Field_DateField('date', trlKwf('Date')))
            ->setDefaultValue(date('Y-m-d'));
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Comment')))
            ->setWidth(250)->setHeight(70);
    }
}
