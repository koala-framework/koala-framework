<?php
class Kwc_Shop_Products extends Kwf_Model_Db
{
    protected $_rowClass = 'Kwc_Shop_Product';
    protected $_table = 'kwc_shop_products';
    protected $_filters = array('pos');
    protected $_dependentModels = array(
        'Prices' => 'Kwc_Shop_ProductPrices'
    );

    protected function _init()
    {
        parent::_init();
        $s = $this->select();
        $s->limit(1);
        $s->order('valid_from', 'DESC');
        $s->where(new Kwf_Model_Select_Expr_Lower('valid_from', new Kwf_DateTime(time())));
        $this->_exprs['current_price'] =
            new Kwf_Model_Select_Expr_Child(
                'Prices',
                new Kwf_Model_Select_Expr_Field('price'),
                $s);

        $this->_exprs['current_price_id'] =
            new Kwf_Model_Select_Expr_Child(
                'Prices',
                new Kwf_Model_Select_Expr_Field('id'),
                $s);
    }
}
