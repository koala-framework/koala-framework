<?php
class Kwc_Shop_Products_Directory_AddToCart_Trl_Component extends Kwc_Form_Trl_Component
{
    public function getAddToCartForm(Kwf_Component_Data $addToCartData)
    {
        return $this->getData()->parent->getComponent()->getItemDirectory()
            ->getChildComponent('_'.$addToCartData->parent->id)->getComponent()->getAddToCartForm();
    }
}
