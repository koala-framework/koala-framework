<?php
class Kwc_Shop_Products_Detail_RelatedProducts_Product_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        $products = array();
        $model = Kwf_Model_Abstract::getInstance('Kwc_Shop_Products');
        $select = $model->select()->order('pos');
        $this->add(new Kwf_Form_Field_Select('product_id', trlKwf('Product')))
            ->setValues($model->getRows($select))
            ->setAllowBlank(false)
            ->setWidth(300);
    }
}
