<?php
class Vpc_Shop_AddToCart_FrontendForm extends Vpc_Shop_AddToCartAbstract_FrontendForm
{
    protected $_product;

    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_Select('amount', trlVps('Amount')))
            ->setAllowBlank(false)
            ->setValues($this->_getAmountValues());
    }

    protected function _getAmountValues($count = 10)
    {
        $ret = array();
        for ($x = 1; $x <= $count; $x++) {
            $ret[$x] = $x;
        }
        return $ret;
    }

    public function setProduct(Vpc_Shop_Product $product)
    {
        $this->_product = $product;
    }

    protected function _beforeInsert(&$row)
    {
        $select = $row->getModel()->select()
            ->whereEquals('shop_order_id', $row->shop_order_id)
            ->whereEquals('shop_product_price_id', $row->shop_product_price_id)
            ->whereEquals('add_component_id', $row->add_component_id)
            ->whereEquals('add_component_class', $row->add_component_class);
        foreach ($row->getSiblingRow(0)->toArray() as $key => $val) {
            $select->whereEquals($key, $val);
        }
        $existingRow = $row->getModel()->getRow($select);
        if ($existingRow) {
            $existingRow->amount += $row->amount;
            $row = $existingRow;
        }
    }
}
