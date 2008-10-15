<?php
class Vpc_Shop_Cart_Checkout_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Shop_Cart_Checkout_Form_Component';
        $ret['generators']['confirm'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Shop_Cart_Checkout_Confirm_Component',
            'name' => trlVps('Send order')
        );
        return $ret;
    }
}
