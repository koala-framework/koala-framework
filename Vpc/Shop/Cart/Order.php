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
        return Vpc_Mail_Recipient_Interface::MAIL_FORMAT_HTML;
    }

    public function getSubTotal()
    {
        $ret = 0;
        foreach ($this->getChildRows('Products') as $op) {
            $data = Vpc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($op->add_component_class);
            $ret += $data->getPrice($op);
        }
        return $ret;
    }

    public function getTotalAmount()
    {
        $ret = 0;
        foreach ($this->getChildRows('Products') as $op) {
            $data = Vpc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($op->add_component_class);
            $ret += $data->getAmount($op);
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

    /**
     * Nur verwenden wenn Bestellung noch nicht abgeschlossen
     */
    public function getProductsDataWithProduct()
    {
        return $this->_getProductsData(true);
    }
    /**
     * Kann immer verwendet werden, auch wenn es add_compoment_id gar nicht mehr gibt
     */
    public function getProductsData()
    {
        return $this->_getProductsData(false);
    }

    private function _getProductsData($includeProduct)
    {
        $ret = array();

        $items = $this->getChildRows('Products');
        $ret = array();
        foreach ($items as $i) {
            $data = Vpc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($i->add_component_class);
            $r = array(
                'additionalOrderData' => $data->getAdditionalOrderData($i),
                'price' => $data->getPrice($i),
                'amount' => $data->getAmount($i),
                'text' => $data->getProductText($i),
            );
            if ($includeProduct) {
                $addComponent = Vps_Component_Data_Root::getInstance()
                                ->getComponentByDbId($i->add_component_id);
                $r['product'] = $addComponent->parent;
            }
            $ret[] = (object)$r;
        }
        return $ret;
    }

    public function getPlaceholders()
    {
        $ret = array();
        $m = new Vps_View_Helper_Money();
        $ret['total'] = $m->money($this->getTotal());
        $ret['orderNumber'] = $this->order_number;

        $plugins = Vpc_Shop_Cart_OrderData::getInstance($this->cart_component_class)
                    ->getShopCartPlugins();
        foreach ($plugins as $plugin) {
            $ret = array_merge($ret, $plugin->getPlaceholders($this));
        }
        return $ret;

    }
}
