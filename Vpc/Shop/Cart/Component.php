<?php
class Vpc_Shop_Cart_Component extends Vpc_Directories_Item_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Shop_Cart_Form_Component';
        $ret['generators']['child']['component']['view'] = 'Vpc_Shop_Cart_View_Component';
        $ret['generators']['detail']['class'] = 'Vpc_Shop_Cart_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_Shop_Cart_Detail_Component';
        $ret['generators']['detail']['model'] = 'Vpc_Shop_Cart_OrderProducts';
        $ret['generators']['checkout'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Shop_Cart_Checkout_Component',
            'name' => trlVps('Checkout')
        );
        $ret['viewCache'] = false;
        $ret['cssClass'] = 'webStandard webForm';
        $ret['componentName'] = trlVps('Shop.Cart');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['countProducts'] = $this->getData()->countChildComponents(array('generator'=>'detail'));
        $ret['checkout'] = $this->getData()->getChildComponent('_checkout');
        return $ret;
    }

    public function getShipping($order = null)
    {
        if (!$order) $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                                        ->getCartOrder();
        if ($order->getTotalAmount() >= 2) {
            return 0;
        } else {
            return 4.5;
        }
    }
    public function getCashOnDeliveryCharge($order = null)
    {
        if (!$order) $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                                        ->getCartOrder();
        if ($order->payment == 'cashOnDelivery') {
            return 6.5;
        } else {
            return 0;
        }
    }
}
