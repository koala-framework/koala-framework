<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;

        $ret['generators']['child']['component']['confirmLink'] = 'Vpc_Shop_Cart_Checkout_Payment_Abstract_ConfirmLink_Component';

        $ret['generators']['confirm'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component',
            'name' => trlVps('Send order')
        );

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

        return $ret;
    }
}
