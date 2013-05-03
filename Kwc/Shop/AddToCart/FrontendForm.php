<?php
class Kwc_Shop_AddToCart_FrontendForm extends Kwc_Shop_AddToCartAbstract_FrontendForm
{
    protected $_product;

    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Kwf_Form_Field_Select('amount', trlKwfStatic('Amount')))
            ->setAllowBlank(false)
            ->setValues($this->_getAmountValues())
            ->setEditable(true);
    }

    protected function _getAmountValues($count = 10)
    {
        $ret = array();
        for ($x = 1; $x <= $count; $x++) {
            $ret[$x] = $x;
        }
        return $ret;
    }

    public function setProduct(Kwc_Shop_Product $product)
    {
        $this->_product = $product;
    }

    protected function _beforeInsert(&$row)
    {
        $select = $this->_getCheckProductRowExistsSelect($row);
        $existingRow = null;
        foreach ($row->getModel()->getRows($select) as $i) {
            $match = true;
            foreach ($row->getSiblingRow(0)->toArray() as $key => $val) {
                if ($key != 'amount' && $i->$key != $row->$key) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $existingRow = $i;
                break;
            }
        }
        if ($existingRow) {
            $existingRow->amount += $row->amount;
            $row = $existingRow;
        }
    }

    protected function _getCheckProductRowExistsSelect($row)
    {
        if (!$row->shop_order_id) return false; //happens for replacements (babytuch)

        $select = $row->getModel()->select()
            ->whereEquals('shop_order_id', $row->shop_order_id)
            ->whereEquals('shop_product_price_id', $row->shop_product_price_id)
            ->whereEquals('add_component_id', $row->add_component_id)
            ->whereEquals('add_component_class', $row->add_component_class);
        return $select;
    }
}
