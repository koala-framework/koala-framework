<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public function confirmOrder($order)
    {
        $order->payment_component_id = $this->getData()->componentId;
        $order->checkout_component_id = $this->getData()->parent->componentId;
        $order->cart_component_class = $this->getData()->parent->parent->componentClass;
        $order->status = 'ordered';
        $order->confirm_mail_sent = date('Y-m-d H:i:s');
        $order->date = date('Y-m-d H:i:s');
        $order->save();

        $plugins = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->chained->parent->parent->componentClass, 'childModel'))
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

    public function sendConfirmMail(Kwc_Shop_Cart_Order $order)
    {
        $mail = $this->getData()->getChildComponent('_mail')->getComponent();
        $data = array(
            'order' => $order,
            'sumRows' => $this->getData()->chained->parent->getComponent()->getSumRows($order)
        );
        $mail->send($order, $data);
    }
}

