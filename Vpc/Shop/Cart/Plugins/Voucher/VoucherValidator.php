<?php
class Vpc_Shop_Cart_Plugins_Voucher_VoucherValidator extends Zend_Validate_Abstract
{
    const NOAMOUNT = 'noAmount';
    const INVALID  = 'invalid';

    public function __construct()
    {
        $this->_messageTemplates[self::NOAMOUNT] = trlVps("'%value%' code was already used");
        $this->_messageTemplates[self::INVALID] = trlVps("'%value%' is not a valid voucher code");
    }

    public function isValid($value)
    {
        $valueString = (string) $value;

        $this->_setValue($valueString);

        $s = new Vps_Model_Select();
        $s->whereEquals('code', $valueString);
        $row = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Plugins_Voucher_Vouchers')->getRow($s);
        if (!$row) {
            $this->_error(self::INVALID);
            return false;
        }

        if ($row->amount - $row->used_amount <= 0) {
            $this->_error(self::NOAMOUNT);
            return false;
        }

        return true;
    }

}