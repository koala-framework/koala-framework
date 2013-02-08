<?php
class Kwc_Shop_Cart_Plugins_Voucher_Redeem_FrontendForm extends Kwf_Form
{
    protected $_modelName = 'Kwc_Shop_Cart_Orders';

    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Kwf_Form_Field_TextField('voucher_code', trlKwfStatic('Voucher Code')))
            ->addValidator(new Kwc_Shop_Cart_Plugins_Voucher_VoucherValidator())
            ->setAllowBlank(false);
    }
}
