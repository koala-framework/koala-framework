<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Trl_Component extends Kwc_Editable_Trl_Component
{
    public function getPlaceholders()
    {
        return $this->getData()->chained->getComponent()->getPlaceholders();
    }
}
