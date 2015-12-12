<?php
class Kwc_Shop_AddToCart_Trl_Form_Component extends Kwc_Shop_AddToCartAbstract_Trl_Form_Component
{
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->shop_product_price_id = $this->getData()->parent->getComponent()->getProductRow()->current_price_id;
    }
}

