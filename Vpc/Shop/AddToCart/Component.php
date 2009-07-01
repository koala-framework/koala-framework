<?php
class Vpc_Shop_AddToCart_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('add to cart');
        $ret['generators']['child']['component']['success'] = 'Vpc_Shop_AddToCart_Success_Component';
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
        $row->shop_product_id = $this->_getProduct()->id;
        $row->add_component_id = $this->getData()->dbId;
    }

    protected function _getProduct()
    {
        return $this->getData()->parent->row;
    }
}
