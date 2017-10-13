<?php
class Kwc_Shop_Cart_Checkout_ProductData_Price extends Kwf_Data_Abstract
{
    public function load($row, array $info = array())
    {
        $data = Kwc_Shop_AddToCartAbstract_OrderProductData::getInstance($row->add_component_class);
        return $data->getPrice($row);
    }
}
