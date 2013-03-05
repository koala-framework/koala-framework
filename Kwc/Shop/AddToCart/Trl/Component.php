<?php
class Kwc_Shop_AddToCart_Trl_Component extends Kwc_Shop_AddToCartAbstract_Trl_Component
{
    public function getProductRow()
    {
        return $this->getData()->chained->parent->row;
    }
}
