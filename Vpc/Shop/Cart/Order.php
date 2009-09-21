<?php
class Vpc_Shop_Cart_Order extends Vps_Model_Db_Row
    implements Vpc_Mail_Recipient_TitleInterface
{
    protected function _afterSave()
    {
        parent::_afterSave();
        if (Vpc_Shop_Cart_Orders::getCartOrderId() == $this->id && $this->status != 'cart') {
            Vpc_Shop_Cart_Orders::resetCartOrderId();
        }
    }
    public function getMailGender()
    {
        return $this->sex;
    }
    public function getMailTitle()
    {
        return $this->title;
    }
    public function getMailFirstname()
    {
        return $this->firstname;
    }
    public function getMailLastname()
    {
        return $this->lastname;
    }
    public function getMailEmail()
    {
        return $this->email;
    }
    public function getMailFormat()
    {
        return Vpc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
    }

    public function getSubTotal()
    {
        $ret = 0;
        foreach ($this->getChildRows('Products') as $op) {
            $p = $op->getParentRow('ProductPrice');
            $ret += $p->price * $op->amount;
        }
        return $ret;
    }

    public function getTotalAmount()
    {
        $ret = 0;
        foreach ($this->getChildRows('Products') as $op) {
            $ret += $op->amount;
        }
        return $ret;
    }

    public function getTotal()
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Shop_Cart_Component')
            ->getChildComponent('_checkout')
            ->getComponent()->getTotal($this);
    }

    public function getOrderNumber()
    {
        return $this->id + 11000;
    }

    public function getSalutation()
    {
        $ret = '';
        if ($this->sex == 'male') {
            $ret .= trlVps('Mr.');
        } else {
            $ret .= trlVps('Ms.');
        }
        $ret .= ' '.trim($this->title.' '.$this->lastname);
        return $ret;
    }
}
