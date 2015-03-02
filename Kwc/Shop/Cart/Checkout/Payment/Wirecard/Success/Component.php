<?php
class Kwc_Shop_Cart_Checkout_Payment_Wirecard_Success_Component extends Kwc_Editable_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['processInput'] = true;
        $ret['generators']['content']['component'] = 'Kwc_Shop_Cart_Checkout_Payment_Abstract_Confirm_Paragraphs_Component';
        return $ret;
    }

    public function getNameForEdit()
    {
        return trlKwf('Shop Confirmation Text') . ' (' .$this->getData()->getSubroot()->id . ') '
            . Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($this->getData()->parent->componentClass, 'componentName'));
    }

    public function processInput($data)
    {
        if (isset($data['paymentState']) && $data['paymentState'] == 'SUCCESS') {
            $wirecardSecret = $this->getData()->getBaseProperty('wirecard.secret');
            if (!$wirecardSecret) {
                throw new Kwf_Exception('Set wirecard setting secret in config!');
            }
            $data['secret'] = $wirecardSecret;
            $responseFingerprintSeed  = "";
            foreach (explode(',', $data['responseFingerprintOrder']) as $key) {
                $responseFingerprintSeed  .= utf8_encode($data[$key]);
            }
            $responseFingerprint = md5($responseFingerprintSeed);
            if ($responseFingerprint == $data['responseFingerprint']) {
                $order = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(
                    $this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'
                ))->getReferencedModel('Order')->getRow($data['order_id']);
                if (!$order) {
                    throw new Kwf_Exception("Order not found!");
                }

                $order->payment_component_id = $this->getData()->parent->componentId;
                $order->checkout_component_id = $this->getData()->parent->parent->componentId;
                $order->cart_component_class = $this->getData()->parent->parent->parent->componentClass;

                $order->status = 'payed';
                $order->date = date('Y-m-d H:i:s');
                $order->payed = date('Y-m-d H:i:s');
                $order->save();

                foreach ($this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->getComponent()->getShopCartPlugins() as $p) {
                    $p->orderConfirmed($order);
                }
                foreach ($order->getChildRows('Products') as $p) {
                    $addComponent = Kwf_Component_Data_Root::getInstance()
                        ->getComponentByDbId($p->add_component_id);
                    $addComponent->getComponent()->orderConfirmed($p);
                }

                $this->getData()->parent->getComponent()->sendConfirmMail($order);
            } else {
                throw new Kwf_Exception('Error by validation of payment');
            }
        } else {
            Kwf_Util_Redirect::redirect($this->getData()->getParentByClass('Kwc_Shop_Component')->url);
        }
    }
}
