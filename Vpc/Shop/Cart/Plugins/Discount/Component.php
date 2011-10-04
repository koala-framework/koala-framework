<?php
class Vpc_Shop_Cart_Plugins_Discount_Component extends Vps_Component_Plugin_Abstract
    implements Vpc_Shop_Cart_Plugins_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Discount');
        return $ret;
    }

    public function getAdditionalSumRows($order, $total)
    {
        if (!$order instanceof Vpc_Shop_Cart_Order) return array();
        if (!$order->discount_amount) return array();

        $amount = -(float)$order->discount_amount;

        return array(array(
            'amount' => $amount,
            'text' => $order->discount_text.':',
            'type' => 'discount'
        ));
    }

    public function alterBackendOrderForm(Vps_Form $form)
    {
        $fs = $form->add(new Vps_Form_Container_FieldSet(trlVps('Discount')));
        $fs->add(new Vps_Form_Field_TextField('discount_text', trlVps('Text')));
        $fs->add(new Vps_Form_Field_NumberField('discount_amount', trlcVps('Amount of Money', 'Amount')))
            ->setComment('€')
            ->setWidth(50);
        $fs->add(new Vps_Form_Field_TextField('discount_comment', trlVps('Comment')));
    }

    public function getPlaceholders(Vpc_Shop_Cart_Order $order) {}
    public function orderConfirmed(Vpc_Shop_Cart_Order $order) {}
}
