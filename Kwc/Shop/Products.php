<?php
class Vpc_Shop_Products extends Vps_Model_Db
{
    protected $_rowClass = 'Vpc_Shop_Product';
    protected $_table = 'vpc_shop_products';
    protected $_filters = array('pos');
    protected $_dependentModels = array(
        'Prices' => 'Vpc_Shop_ProductPrices'
    );

    protected function _init()
    {
        parent::_init();
        $s = $this->select();
        $s->limit(1);
        $s->order('valid_from', 'DESC');
        $s->where(new Vps_Model_Select_Expr_Lower('valid_from', new Vps_DateTime(time())));
        $this->_exprs['current_price'] =
            new Vps_Model_Select_Expr_Child(
                'Prices',
                new Vps_Model_Select_Expr_Field('price'),
                $s);

        $this->_exprs['current_price_id'] =
            new Vps_Model_Select_Expr_Child(
                'Prices',
                new Vps_Model_Select_Expr_Field('id'),
                $s);
    }
}
