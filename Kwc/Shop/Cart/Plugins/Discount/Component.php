<?php
class Kwc_Shop_Cart_Plugins_Discount_Component extends Kwf_Component_Plugin_Abstract
    implements Kwc_Shop_Cart_Plugins_Interface
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Discount');
        return $ret;
    }

    public function getAdditionalSumRows($order, $total)
    {
        if (!$order instanceof Kwc_Shop_Cart_Order) return array();
        if (!$order->discount_amount) return array();

        $amount = -(float)$order->discount_amount;

        return array(array(
            'amount' => $amount,
            'text' => $order->discount_text.':',
            'type' => 'discount'
        ));
    }

    public function alterBackendOrderForm(Kwf_Form $form)
    {
        $fs = $form->add(new Kwf_Form_Container_FieldSet(trlKwf('Discount')));
        $fs->add(new Kwf_Form_Field_TextField('discount_text', trlKwf('Text')));
        $fs->add(new Kwf_Form_Field_NumberField('discount_amount', trlcKwf('Amount of Money', 'Amount')))
            ->setComment('€')
            ->setWidth(50);
        $fs->add(new Kwf_Form_Field_TextField('discount_comment', trlKwf('Comment')));
    }

    public function getPlaceholders(Kwc_Shop_Cart_Order $order) { return array(); }
    public function orderConfirmed(Kwc_Shop_Cart_Order $order) {}
}
