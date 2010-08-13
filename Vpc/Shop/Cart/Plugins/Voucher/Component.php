<?php
class Vpc_Shop_Cart_Plugins_Voucher_Component extends Vps_Component_Plugin_Abstract
    implements Vpc_Shop_Cart_Plugins_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasResources'] = true;
        $ret['componentName'] = trlVps('Vouchers');
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Shop/Cart/Plugins/Voucher/Panel.js';
        return $ret;
    }

    public function alterCheckoutForm(Vpc_Shop_Cart_Checkout_Form_Form $form)
    {
        $form->insertAfter('payment', new Vps_Form_Field_TextField('voucher_code', trlVps('Voucher Code')))
            ->addValidator(new Vpc_Shop_Cart_Plugins_Voucher_VoucherValidator());
    }

    public function getAdditionalSumRows(Vpc_Shop_Cart_Order $order, $total)
    {
        if (!$order->voucher_code) return array();

        $s = new Vps_Model_Select();
        $s->whereEquals('code', $order->voucher_code);
        $row = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Plugins_Voucher_Vouchers')->getRow($s);

        if (!$row || $row->amount - $row->used_amount <= 0) return array();

        return array(array(
            'amount' => -min($total, $row->amount - $row->used_amount),
            'text' => trlVps('Voucher').':',
            'type' => 'voucher'
        ));
    }

    public function orderConfirmed(Vpc_Shop_Cart_Order $order)
    {
        if (!$order->voucher_code) return;

        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($order->checkout_component_id);
        foreach ($c->getComponent()->getSumRows($order) as $sumRow) {
            if (isset($sumRow['type']) && $sumRow['type'] == 'voucher') {
                $s = new Vps_Model_Select();
                $s->whereEquals('code', $order->voucher_code);
                $row = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Plugins_Voucher_Vouchers')->getRow($s);
                $h = $row->createChildRow('history');
                $h->amount = -$sumRow['amount'];
                $h->order_id = $order->id;
                $h->date = $order->date;
                $h->save();
                break;
            }
        }
    }
}
