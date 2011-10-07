<?php
class Kwc_Shop_Cart_Checkout_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_Shop_Cart_Checkout_Form_Component';

        $ret['generators']['payment'] = array(
            'class' => 'Kwf_Component_Generator_PseudoPage_Static',
            'component' => array(
                'prePayment' => 'Kwc_Shop_Cart_Checkout_Payment_PrePayment_Component',
                'cashOnDelivery' => 'Kwc_Shop_Cart_Checkout_Payment_CashOnDelivery_Component',
                'payPal' => 'Kwc_Shop_Cart_Checkout_Payment_PayPal_Component',
                'none' => 'Kwc_Shop_Cart_Checkout_Payment_None_Component'
            )
        );
        $ret['cssClass'] = 'webForm webStandard';
        $ret['placeholder']['backToCart'] = trlKwf('Back to cart');

        $ret['shipping'] = 0;

        $ret['flags']['hasResources'] = true;

        $ret['assetsAdmin']['dep'][] = 'ExtFormDateField';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Shop/Cart/Checkout/OrdersPanel.js';

        return $ret;
    }

    public final function getTotal($order)
    {
        return Kwc_Shop_Cart_OrderData::getInstance($this->getData()->parent->componentClass)->getTotal($order);
    }

    public final function getSumRows($order)
    {
        return Kwc_Shop_Cart_OrderData::getInstance($this->getData()->parent->componentClass)->getSumRows($order);
    }
}
