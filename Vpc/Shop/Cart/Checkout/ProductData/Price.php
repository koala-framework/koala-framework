<?php
class Vpc_Shop_Cart_Checkout_ProductData_Price extends Vps_Data_Abstract
{
    public function load($row)
    {
        $data = Vpc_Shop_AddToCartAbstract_OrderProductData::getInstance($row->add_component_class);
        return $data->getPrice($row);
    }
}