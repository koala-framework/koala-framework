<?php
abstract class Vpc_Shop_AddToCartAbstract_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('add to cart');
        $ret['generators']['child']['component']['success'] = 'Vpc_Shop_AddToCartAbstract_Success_Component';
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
    }

    //TODO ned optimal, aber wir müssen mehere forms machen können
    public function createForm()
    {
        unset($this->_form);
        $this->_initForm();
        $ret = $this->_form;
        return $ret;
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
    }

    public function getAdditionalOrderData(Vpc_Shop_Cart_OrderProduct $row)
    {
        return array();
    }

    abstract public function getPrice(Vpc_Shop_Cart_OrderProduct $orderProduct);
    abstract public function getAmount(Vpc_Shop_Cart_OrderProduct $orderProduct);

    public function orderConfirmed(Vpc_Shop_Cart_OrderProduct $orderProduct)
    {
    }
}
