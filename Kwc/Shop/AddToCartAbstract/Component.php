<?php
abstract class Kwc_Shop_AddToCartAbstract_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwfStatic('add to cart');
        $ret['generators']['child']['component']['success'] = 'Kwc_Shop_AddToCartAbstract_Success_Component';
        $ret['orderProductData'] = 'Kwc_Shop_AddToCartAbstract_OrderProductData';
        $ret['productTypeText'] = null;
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $cart = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass(
                array('Kwc_Shop_Cart_Component', 'Kwc_Shop_Cart_Trl_Component'),
                array('subroot'=>$this->getData(), 'ignoreVisible' => true)
            );
        if (!$cart) throw new Kwf_Exception_Client(trlKwf('Need cart for shop but could not find it. Please add in Admin.'));
        $m = $cart->getComponent()->getOrderProductsModel();
        $this->_form->setModel($m);
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        if ($this->_form->getId()) {
            $ret['placeholder']['submitButton'] = $this->data->trlKwf('Update');
        }
        return $ret;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $orders = Kwf_Model_Abstract::getInstance('Kwc_Shop_Cart_Orders');
        $row->shop_order_id = $orders->getCartOrderAndSave()->id;
        $row->add_component_id = $this->getData()->dbId;
        $row->add_component_class = $this->getData()->componentClass;
    }

    public final function getAdditionalOrderData(Kwc_Shop_Cart_OrderProduct $orderProduct)
    {
        return Kwc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->getAdditionalOrderData($orderProduct);
    }

    public function getPrice(Kwc_Shop_Cart_OrderProduct $orderProduct)
    {
        return Kwc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->getPrice($orderProduct);
    }

    public final function getAmount(Kwc_Shop_Cart_OrderProduct $orderProduct)
    {
        return Kwc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->getAmount($orderProduct);
    }

    public final function orderConfirmed(Kwc_Shop_Cart_OrderProduct $orderProduct)
    {
        Kwc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->orderConfirmed($orderProduct);
    }

    public final function getProductText(Kwc_Shop_Cart_OrderProduct $orderProduct)
    {
        $ret = Kwc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->getProductText($orderProduct);
        if (is_instance_of($this->getData()->parent->componentClass, 'Kwc_Shop_AddToCartAbstract_Trl_Component')) {
            $ret = $this->getData()->getParentByClass('Kwc_Shop_Products_Detail_Trl_Component')->row->title;
        }
        return $ret;
    }

    public function getProduct()
    {
        $ret = $this->getData()->parent;
        if (is_instance_of($ret->componentClass, 'Kwc_Shop_AddToCartAbstract_Trl_Component')) {
            $ret = $ret->parent;
        }
        return $ret;
    }
}
