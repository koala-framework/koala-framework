<?php
class Kwc_Shop_Cart_Order extends Kwf_Model_Db_Row
    implements Kwc_Mail_Recipient_TitleInterface
{
    protected $_groupNumbersByCheckoutComponent = false;

    public function generateInvoiceNumber()
    {
        $s = $this->getModel()->select();
        $s->limit(1);
        $s->order('invoice_number', 'DESC');
        if ($this->_groupNumbersByCheckoutComponent) {
            $s->whereEquals('checkout_component_id', $this->checkout_component_id);
        }
        $row = $this->getModel()->getRow($s);
        $maxNumber = 0;
        if ($row) $maxNumber = $row->invoice_number;
        $this->invoice_number = $maxNumber + 1;
    }

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
            if ($this->_groupNumbersByCheckoutComponent) {
                $s->whereEquals('checkout_component_id', $this->checkout_component_id);
            }
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

    //override in addToCart
    public final function getProductText($orderProduct)
    {
        $data = Kwc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($orderProduct->add_component_class);
        return $data->getProductText($orderProduct);
    }

    //override _getProductPrice
    public final function getProductPrice($orderProduct)
    {
        return $this->_getProductPrice($orderProduct);
    }

    //override to implement eg. excl. vat prices for the whole order
    protected function _getProductPrice($orderProduct)
    {
        $data = Kwc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($orderProduct->add_component_class);
        return $data->getPrice($orderProduct);
    }

    public function getSubTotal()
    {
        $ret = 0;
        foreach ($this->getChildRows('Products') as $op) {
            $ret += $this->_getProductPrice($op);
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

    //kann überschrieben werden um shipping zB abhängig von bestellmenge zu machen
    protected function _getShipping()
    {
        return Kwc_Abstract::getSetting(
            Kwc_Abstract::getChildComponentClass($this->getModel()->getCartComponentClass(), 'checkout'),
            'shipping'
        );
    }

    //return false to completely hide shipping
    protected function _hasShipping()
    {
        return true;
    }

    public function getTotal()
    {
        $ret = $this->getSubTotal();
        if ($this->_hasShipping($this)) {
            $ret += $this->_getShipping($this);
        }
        foreach ($this->_getAdditionalSumRows($ret) as $r) {
            $ret += $r['amount'];
        }
        return $ret;
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

    // if product is not available in sitetree anymore it is deleted (also called by Kwc_Shop_Cart_Component)
    private function _getProductsData(Kwf_Component_Data $subroot = null)
    {
        $ret = array();

        $items = $this->getChildRows('Products');
        $ret = array();

        foreach ($items as $i) {
            $data = Kwc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($i->add_component_class);
            $r = array(
                'id' => $i->id,
                'additionalOrderData' => $data->getAdditionalOrderData($i),
                'price' => $this->_getProductPrice($i),
                'amount' => $data->getAmount($i),
                'text' => $data->getProductText($i),
            );
            if ($subroot) {
                $addComponent = Kwc_Shop_AddToCartAbstract_OrderProductData::getAddComponentByDbId(
                    $i->add_component_id, $subroot
                );
                if (!$addComponent) {
                    //product doesn't exist anymore, also delete from cart
                    $i->delete();
                    continue;
                } else {
                    $r['product'] = $addComponent->parent;
                    $r['text'] = $data->getProductTextDynamic($i);
                }
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

        $plugins = $this->getModel()->getShopCartPlugins();
        foreach ($plugins as $plugin) {
            $ret = array_merge($ret, $plugin->getPlaceholders($this));
        }
        return $ret;
    }

    //kann überschrieben werden um zeilen für alle payments zu ändern
    protected function _getAdditionalSumRows($total)
    {
        $ret = array();
        $payments = Kwc_Abstract::getChildComponentClasses(
            Kwc_Abstract::getChildComponentClass($this->getModel()->getCartComponentClass(), 'checkout'), 'payment');
        if (isset($payments[$this->payment])) {
            $rows = Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderData
                ::getInstance($payments[$this->payment])
                ->getAdditionalSumRows($this);
            foreach ($rows as $r) $total += $r['amount'];
            $ret = array_merge($ret, $rows);
        }
        foreach ($this->getModel()->getShopCartPlugins() as $p) {
            $rows = $p->getAdditionalSumRows($this, $total);
            foreach ($rows as $r) $total += $r['amount'];
            $ret = array_merge($ret, $rows);
        }
        return $ret;
    }

    public function getSumRows()
    {
        $ret = array();
        $subTotal = $this->getSubTotal();
        $ret[] = array(
            'class' => 'valueOfGoods',
            'text' => trlKwfStatic('value of goods').':',
            'amount' => $subTotal
        );
        if (Kwc_Abstract::getSetting($this->getModel()->getCartComponentClass(), 'vatRate')) {
            $vat = 1+Kwc_Abstract::getSetting($this->getModel()->getCartComponentClass(), 'vatRate');
            $ret[] = array(
                'text' => trlKwfStatic('net amount').':',
                'amount' => round($subTotal/$vat, 2)
            );
            $ret[] = array(
                'text' => trlKwfStatic('+{0}% VAT', ($vat-1 )*100).':',
                'amount' => round($subTotal - $subTotal/$vat, 2)
            );
        }
        $shipping = 0;
        if ($this->_hasShipping($this)) {
            $shipping = $this->_getShipping($this);
            $vat = 1+Kwc_Abstract::getSetting($this->getModel()->getCartComponentClass(), 'vatRateShipping');
            $ret[] = array(
                'class' => 'shippingHandling',
                'text' => trlKwfStatic('Shipping and Handling').':',
                'amount' => round($shipping/$vat, 2)
            );
            if (Kwc_Abstract::getSetting($this->getModel()->getCartComponentClass(), 'vatRateShipping')) {
                $ret[] = array(
                    'text' => trlKwfStatic('+{0}% VAT', ($vat-1 )*100).':',
                    'amount' => round($shipping - $shipping/$vat, 2)
                );
            }
        }
        $ret = array_merge($ret, $this->_getAdditionalSumRows($subTotal+$shipping));
        $ret[] = array(
            'class' => 'totalAmount',
            'text' => trlKwfStatic('Total Amount').':',
            'amount' => $this->getTotal($this)
        );
        return $ret;
    }
}
