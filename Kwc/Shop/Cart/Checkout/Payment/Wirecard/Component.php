<?php
/**
 * set preLoginIgnore for wirecard confirm url in config: preLoginIgnore.wirecardConfirm = url
 **/
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_Component extends Kwc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Wirecard');
        // Delete confirm because of wirecard dispatch confirm url
        unset($ret['generators']['confirm']);
        $ret['generators']['child']['component']['confirmLink'] = 'Kwc_Shop_Cart_Checkout_Payment_Wirecard_ConfirmLink_Component';
        $ret['generators']['cancel'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Payment_Wirecard_Cancel_Component',
            'name' => trlKwfStatic('Cancel')
        );

        $ret['generators']['failure'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Payment_Wirecard_Failure_Component',
            'name' => trlKwfStatic('Failure')
        );

        $ret['generators']['success'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Payment_Wirecard_Success_Component',
            'name' => trlKwfStatic('Success')
        );

        $ret['generators']['ipn'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Payment_Wirecard_Ipn_Component'
        );

        /**
         * Possible types are:
         * BANCONTACT_MISTERCASH, C2P (Click2Pay), CCARD (Credit Card), EKONTO, ELV (Electronic Funds Transfer),
         * EPS (EPS e-payment), GIROPAY, IDL (iDEAL), INSTALLMENT, INSTANTBANK, INVOICE, MAESTRO,
         * MONETA, MPASS, PRZELEWY24, PAYPAL, PBX (Paybox), POLI, PSC (Paysafecard), QUICK, SKRILLDIRECT,
         * SKRILLWALLET, SOFORTUEBERWEISUNG
         **/
        $ret['paymentType'] = 'SELECT';
        return $ret;
    }
}
