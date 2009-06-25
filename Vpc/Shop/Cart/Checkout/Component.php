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
                'payPal' => 'Vpc_Shop_Cart_Checkout_Payment_PayPal_Component',
            )
        );
        $ret['cssClass'] = 'webForm webStandard';
        return $ret;
    }
}
