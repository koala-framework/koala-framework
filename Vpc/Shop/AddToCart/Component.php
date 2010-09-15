<?php
class Vpc_Shop_AddToCart_Component extends Vpc_Shop_AddToCartAbstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['orderProductData'] = 'Vpc_Shop_AddToCart_OrderProductData';
        $ret['productTypeText'] = trlVps('Product');
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setProduct($this->_getProduct());
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->shop_product_price_id = $this->_getProduct()->current_price_id;
    }

    protected function _getProduct()
    {
        if (isset($this->getData()->row)) {
            //wenn direkt von table generator erstellt
            return $this->getData()->row;
        }
        return $this->getData()->parent->row;
    }
}
