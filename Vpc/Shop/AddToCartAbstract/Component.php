<?php
abstract class Vpc_Shop_AddToCartAbstract_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('add to cart');
        $ret['generators']['child']['component']['success'] = 'Vpc_Shop_AddToCartAbstract_Success_Component';
        $ret['orderProductData'] = 'Vpc_Shop_AddToCartAbstract_OrderProductData';
        $ret['productTypeText'] = null;
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $m = Vps_Component_Data_Root::getInstance()->getComponentByClass('Vpc_Shop_Cart_Component')
                ->getComponent()->getChildModel();
        $this->_form->setModel($m);
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if ($this->_form->getId()) {
            $ret['placeholder']['submitButton'] = trlVps('Update');
        }
        return $ret;
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $orders = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders');
        $row->shop_order_id = $orders->getCartOrderAndSave()->id;
        $row->add_component_id = $this->getData()->dbId;
        $row->add_component_class = $this->getData()->componentClass;
    }

    public final function getAdditionalOrderData(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->getAdditionalOrderData($orderProduct);
    }

    public function getPrice(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->getPrice($orderProduct);
    }

    public final function getAmount(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->getAmount($orderProduct);
    }

    public final function orderConfirmed(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->orderConfirmed($orderProduct);
    }

    public final function getProductText(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
        return Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($this->getData()->componentClass)
            ->getProductText($orderProduct);
    }
}
