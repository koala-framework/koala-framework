<?php
class Vpc_Shop_Cart_Checkout_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Shop_Cart_Checkout_Form_Component';

        $ret['generators']['payment'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Static',
            'component' => array(
                'prePayment' => 'Vpc_Shop_Cart_Checkout_Payment_PrePayment_Component',
                'cashOnDelivery' => 'Vpc_Shop_Cart_Checkout_Payment_CashOnDelivery_Component',
                'payPal' => 'Vpc_Shop_Cart_Checkout_Payment_PayPal_Component',
                'none' => 'Vpc_Shop_Cart_Checkout_Payment_None_Component'
            )
        );
        $ret['cssClass'] = 'webForm webStandard';
        $ret['placeholder']['backToCart'] = trlVps('Back to cart');

        $ret['shipping'] = 0;

        $ret['flags']['hasResources'] = true;

        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Shop/Cart/Checkout/OrdersPanel.js';

        return $ret;
    }

    public final function getTotal($order)
    {
        return Vpc_Shop_Cart_OrderData::getInstance($this->getData()->parent->componentClass)->getTotal($order);
    }

    public final function getSumRows($order)
    {
        return Vpc_Shop_Cart_OrderData::getInstance($this->getData()->parent->componentClass)->getSumRows($order);
    }
}
