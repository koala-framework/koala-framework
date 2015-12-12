<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_Success_Trl_Component extends Kwc_Chained_Trl_Component
{
    public function getPlaceholders()
    {
        return $this->getData()->chained->getComponent()->getPlaceholders();
    }
}

