<?php
class Vpc_Shop_Cart_Plugins_Voucher_VoucherController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vpc_Shop_Cart_Plugins_Voucher_Vouchers';
    protected $_permissions = array('add', 'save');

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->add(new Vps_Form_Field_ShowField('code', trlVps('Code')));
        $this->_form->add(new Vps_Form_Field_NumberField('amount', trlcVps('Amount of Money', 'Amount')))
            ->setWidth(50)
            ->setComment('€');
        $this->_form->add(new Vps_Form_Field_DateField('date', trlVps('Date')))
            ->setDefaultValue(date('Y-m-d'));
        $this->_form->add(new Vps_Form_Field_TextArea('comment', trlVps('Comment')))
            ->setWidth(250)->setHeight(70);
    }
}
