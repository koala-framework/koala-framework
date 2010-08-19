<?php
class Vpc_Shop_Cart_Plugins_Voucher_Redeem_Form extends Vpc_Abstract_Form
{
    protected $_modelName = 'Vpc_Shop_Cart_Orders';

    protected function _initFields()
    {
        parent::_initFields();
        $this->fields->add(new Vps_Form_Field_TextField('voucher_code', trlVps('Voucher Code')))
            ->addValidator(new Vpc_Shop_Cart_Plugins_Voucher_VoucherValidator())
            ->setAllowBlank(false);
    }
}
