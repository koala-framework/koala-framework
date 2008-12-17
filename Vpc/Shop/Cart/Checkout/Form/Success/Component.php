<?php
class Vpc_Shop_Cart_Checkout_Form_Success_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $checkout = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass(
                'Vpc_Shop_Cart_Component',
                array('subroot' => $this->getData())
            )
            ->getChildComponent('_checkout');

        $ret['order'] = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();
        $ret['orderProducts'] = $ret['order']->getChildRows('Products');

        $ret['confirm'] = $checkout->getChildComponent('_confirm');

        return $ret;
    }
}
