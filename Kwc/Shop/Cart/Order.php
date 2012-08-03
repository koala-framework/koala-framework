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

    public function getSubTotal()
    {
        $ret = 0;
        foreach ($this->getChildRows('Products') as $op) {
            $data = Kwc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($op->add_component_class);
            $ret += $data->getPrice($op);
        }
        return $ret;
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

    public function getTotal()
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
    public function getProductsDataWithProduct(Kwf_Component_Data $subroot)
    {
        return $this->_getProductsData($subroot);
    }
    /**
     * Kann immer verwendet werden, auch wenn es add_compoment_id gar nicht mehr gibt
     */
    public function getProductsData()
    {
        return $this->_getProductsData(null);
    }

    private function _getProductsData(Kwf_Component_Data $subroot = null)
    {
        $ret = array();

        $items = $this->getChildRows('Products');
        $ret = array();
        foreach ($items as $i) {
            $data = Kwc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($i->add_component_class);
            $r = array(
                'additionalOrderData' => $data->getAdditionalOrderData($i),
                'price' => $data->getPrice($i),
                'amount' => $data->getAmount($i),
                'text' => $data->getProductText($i),
            );
            if ($subroot) {
                $addComponent = Kwf_Component_Data_Root::getInstance()
                                ->getComponentByDbId($i->add_component_id, array('subroot' => $subroot));
                $r['product'] = $addComponent->parent;
            }
            $ret[] = (object)$r;
        }
        return $ret;
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
