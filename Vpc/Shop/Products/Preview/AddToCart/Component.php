<?php
class Vpc_Shop_Products_Preview_AddToCart_Component extends Vpc_Shop_AddToCart_Component
{
    protected function _initForm()
    {
        $this->_form =
        parent::_initForm();
    }

    protected function _getProduct()
    {
        return $this->getData()->row;
    }
}