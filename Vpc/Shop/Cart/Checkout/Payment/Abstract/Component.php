<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;

        $ret['generators']['child']['component']['orderHeader'] = 'Vpc_Shop_Cart_Checkout_Payment_Abstract_OrderHeader_Component';
        $ret['generators']['child']['component']['orderTable'] = 'Vpc_Shop_Cart_Checkout_Payment_Abstract_OrderTable_Component';
        $ret['generators']['child']['component']['confirmLink'] = 'Vpc_Shop_Cart_Checkout_Payment_Abstract_ConfirmLink_Component';

        $ret['generators']['confirm'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component',
            'name' => trlVps('Send order')
        );

        $ret['generators']['mail'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Component',
        );
        $ret['generators']['shippedMail'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Component',
        );

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['order'] = $this->_getOrder();
        $ret['orderProducts'] = $ret['order']->getChildRows('Products');
        $ret['sumRows'] = $this->_getSumRows($this->_getOrder());
        $ret['paymentTypeText'] = $this->_getSetting('componentName');
        return $ret;
    }

    protected function _getOrder()
    {
        return Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')
                            ->getCartOrder();
    }

    //kann überschrieben werden um zeilen pro payment zu ändern
    protected function _getSumRows($order)
    {
        return $this->getData()->parent->getComponent()->getSumRows($order);
    }

    //da kann zB eine Nachnahmegebühr zurückgegeben werden
    //darf nur von Vpc_Shop_Cart_Checkout_Component::getAdditionalSumRows() aufgerufen werden!
    public function getAdditionalSumRows()
    {
        return array();
    }


    //da kann zB eine Nachnahmegebühr zurückgegeben werden
    //darf nur von Vpc_Shop_Cart_Checkout_Component::getAdditionalSum() aufgerufen werden!
    public function getAdditionalSum($order)
    {
        return 0;
    }

    public function sendConfirmMail($order)
    {
        $mail = $this->getData()->getChildComponent('-mail')->getComponent();
        $data = array(
            'order' => $order,
            'sumRows' => $this->getData()->parent->getComponent()->getSumRows($order)
        );
        $mail->send($order, $data);
    }

    public function confirmOrder($order)
    {
        $order->payment_component_id = $this->getData()->componentId;
        $order->checkout_component_id = $this->getData()->parent->componentId;
        $order->status = 'ordered';
        $order->date = date('Y-m-d H:i:s');
        $order->save();

        $this->sendConfirmMail($order);
    }
}
