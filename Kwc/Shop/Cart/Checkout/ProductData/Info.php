<?php
class Kwc_Shop_Cart_Checkout_ProductData_Info extends Kwf_Data_Abstract
{
    public function load($row)
    {
        $data = Kwc_Shop_AddToCartAbstract_OrderProductData::getInstance($row->add_component_class);
        $parts = array();
        foreach ($data->getAdditionalOrderData($row) as $data) {
            if ($data['class'] == 'amount') continue;
            $parts[] = $data['name'] . ': ' . $data['value'];
        }
        return implode(', ', $parts);
    }
}