<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;

        $ret['generators']['child']['component']['orderHeader'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderHeader_Component';
        $ret['generators']['child']['component']['orderTable'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderTable_Component';
        $ret['generators']['child']['component']['confirmLink'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_ConfirmLink_Component';

        $ret['generators']['confirm'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Component',
            'name' => trlKwfStatic('Send order')
        );

        $ret['generators']['mail'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Mail_Component',
        );
        $ret['generators']['shippedMail'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Payment_Abstract_ShippedMail_Component',
        );

        $ret['orderData'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderData';

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['order'] = $this->_getOrder();
        $ret['orderProducts'] = $ret['order']->getChildRows('Products');
        $ret['sumRows'] = $this->_getSumRows($this->_getOrder());
        $ret['paymentTypeText'] = $this->_getSetting('componentName');
        return $ret;
    }

    protected function _getOrder()
    {
        return Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'))
            ->getReferencedModel('Order')->getCartOrder();
    }

    //kann überschrieben werden um zeilen pro payment zu ändern
    protected function _getSumRows($order)
    {
        return $this->getData()->parent->getComponent()->getSumRows($order);
    }

    public function sendConfirmMail(Kwc_Shop_Cart_Order $order)
    {
        $mail = $this->getData()->getChildComponent('-mail')->getComponent();
        $data = array(
            'order' => $order,
            'sumRows' => $this->getData()->parent->getComponent()->getSumRows($order)
        );
        $mail->send($order, $data);
    }

    //diese fkt wird bei paypal nicht verwendet!
    //wenn also da was hinzugefuegt wird muss das bei paypal auch gemacht werden
    public function confirmOrder($order)
    {
        $order->payment_component_id = $this->getData()->componentId;
        $order->checkout_component_id = $this->getData()->parent->componentId;
        $order->cart_component_class = $this->getData()->parent->parent->componentClass;
        $order->status = 'ordered';
        $order->confirm_mail_sent = date('Y-m-d H:i:s');
        $order->date = date('Y-m-d H:i:s');
        $order->save();

        $plugins = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->parent->parent->componentClass, 'childModel'))
            ->getReferencedModel('Order')->getShopCartPlugins();
        foreach ($plugins as $p) {
            $p->orderConfirmed($order);
        }
        foreach ($order->getChildRows('Products') as $p) {
            $addComponent = Kwc_Shop_AddToCartAbstract_OrderProductData::getAddComponentByDbId(
                $p->add_component_id, $this->getData()
            );
            $addComponent->getComponent()->orderConfirmed($p);
        }

        $this->sendConfirmMail($order);
    }
}
