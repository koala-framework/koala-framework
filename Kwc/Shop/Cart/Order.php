<?php
class Kwc_Shop_Cart_Order extends Kwf_Model_Db_Row
    implements Kwc_Mail_Recipient_TitleInterface
{
    protected function _afterSave()
    {
        parent::_afterSave();
        if (Kwc_Shop_Cart_Orders::getCartOrderId() == $this->id && $this->status != 'cart') {
            Kwc_Shop_Cart_Orders::resetCartOrderId();
        }
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->status != 'cart' && !$this->number) {
            $s = $this->getModel()->select();
            $s->limit(1);
            $s->order('number', 'DESC');
            $row = $this->getModel()->getRow($s);
            $maxNumber = 0;
            if ($row) $maxNumber = $row->number;
            $this->number = $maxNumber + 1;
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
        return Kwc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
    }

    public final function getSubTotal()
    {
        return Kwc_Shop_Cart_OrderData::getInstance($this->cart_component_class)->getSubTotal($this);
    }

    public function getTotalAmount()
    {
        $ret = 0;
        foreach ($this->getChildRows('Products') as $op) {
            $data = Kwc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($op->add_component_class);
            $ret += $data->getAmount($op);
        }
        return $ret;
    }

    public final function getTotal()
    {
        return Kwc_Shop_Cart_OrderData::getInstance($this->cart_component_class)->getTotal($this);
    }

    public function getSalutation()
    {
        $ret = '';
        if ($this->sex == 'male') {
            $ret .= trlKwf('Mr.');
        } else {
            $ret .= trlKwf('Ms.');
        }
        $ret .= ' '.trim($this->title.' '.$this->lastname);
        return $ret;
    }

    /**
     * Nur verwenden wenn Bestellung noch nicht abgeschlossen
     */
    public final function getProductsDataWithProduct(Kwf_Component_Data $subroot)
    {
        return Kwc_Shop_Cart_OrderData::getInstance($this->cart_component_class)->getProductsData($this, $subroot);
    }

    /**
     * Kann immer verwendet werden, auch wenn es add_compoment_id gar nicht mehr gibt
     */
    public final function getProductsData()
    {
        return Kwc_Shop_Cart_OrderData::getInstance($this->cart_component_class)->getProductsData($this, null);
    }

    public function getPlaceholders()
    {
        $ret = array();
        $m = new Kwf_View_Helper_Money();
        $ret['total'] = $m->money($this->getTotal());
        $ret['orderNumber'] = $this->order_number;

        $plugins = Kwc_Shop_Cart_OrderData::getInstance($this->cart_component_class)
                    ->getShopCartPlugins();
        foreach ($plugins as $plugin) {
            $ret = array_merge($ret, $plugin->getPlaceholders($this));
        }
        return $ret;

    }
}
