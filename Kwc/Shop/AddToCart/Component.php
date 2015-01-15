<?php
class Kwc_Shop_AddToCart_Component extends Kwc_Shop_AddToCartAbstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['orderProductData'] = 'Kwc_Shop_AddToCart_OrderProductData';
        $ret['productTypeText'] = trlKwfStatic('Product');
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setProduct($this->_getProduct());
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
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
        return $this->getData()->parent->getComponent()->getProductRow();
    }
}
