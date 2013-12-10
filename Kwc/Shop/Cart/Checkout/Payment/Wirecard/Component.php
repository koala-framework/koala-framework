<?php
/**
 * Set Wirecard dispatch in bootstrap: Kwf_Util_Wirecard::dispatch('Kwc_Shop_Cart_Checkout_Payment_Wirecard_Model');
 * And preLoginIgnore for wirecard confirm url in config: preLoginIgnore.wirecardConfirm = /wirecard_confirm
 **/
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_Component extends Kwc_Shop_Cart_Checkout_Payment_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Wirecard');
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

        $ret['paymentType'] = 'SELECT';
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        parent::validateSettings($settings, $componentClass);
        if (!Kwf_Config::getValue('wirecard.secret')) throw new Kwf_Exception('Set wirecard secret in config');
        if (!Kwf_Config::getValue('wirecard.customerId')) throw new Kwf_Exception('Set wirecard customerId in config');
    }
}
