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

        $ret['order'] = $this->_getOrder();
        $ret['orderProducts'] = $ret['order']->getChildRows('Products');

        $ret['sumRows'] = $this->_getSumRows();

        $ret['paymentTypeText'] = $this->_getSetting('componentName');

        return $ret;
    }

    protected function _getOrder()
    {
        return Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();
    }

    //kann überschrieben werden um zeilen pro payment zu ändern
    protected function _getSumRows()
    {
        return $this->getData()->parent->getComponent()->getSumRows($this->_getOrder());
    }

    //da kann zB eine Nachnahmegebühr zurückgegeben werden
    //darf nur von Vpc_Shop_Cart_Checkout_Component::getAdditionalSumRows() aufgerufen werden!
    public function getAdditionalSumRows()
    {
        return array();
    }
}
