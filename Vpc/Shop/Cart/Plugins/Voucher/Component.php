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

    public function getAdditionalSumRows(Vpc_Shop_Cart_Order $order, $total)
    {
        if (!$order->voucher_code) return array();

        $text = trlVps('Voucher');
        if ($order->voucher_amount) {
            $amount = -(float)$order->voucher_amount;
        } else {

            $s = new Vps_Model_Select();
            $s->whereEquals('code', $order->voucher_code);
            $row = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Plugins_Voucher_Vouchers')->getRow($s);

            if (!$row || $row->amount - $row->used_amount <= 0) return array();

            $amount = -min($total, $row->amount - $row->used_amount);
            $remainingAmount = $row->amount - $row->used_amount + $amount;
            if ($remainingAmount > 0) {
                $text .= ' ('.trlVps('Remaining Amount {0}', Vps_View_Helper_Money::money($remainingAmount)).')';
            }
        }

        return array(array(
            'amount' => $amount,
            'text' => $text.':',
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
                $remainingAmount = $row->amount - $row->used_amount + $sumRow['amount'];
                $h = $row->createChildRow('history');
                $h->amount = -$sumRow['amount'];
                $h->order_id = $order->id;
                $h->date = $order->date;
                $h->comment = trlVps('Order').' '.$order->order_number;
                $h->save();

                //verbrauchten betrag auch noch bei der order speichern
                $order->voucher_amount = (float)$h->amount;
                $order->voucher_remaining_amount = (float)$remainingAmount;
                $order->save();
                break;
            }
        }
    }

    public function alterBackendOrderForm(Vps_Form $form)
    {
        $fs = $form->add(new Vps_Form_Container_FieldSet(trlVps('Voucher')));
        $fs->add(new Vps_Form_Field_TextField('voucher_code', trlVps('Code')));
        $fs->add(new Vps_Form_Field_NumberField('voucher_amount', trlcVps('Amount of Money', 'Amount')))
            ->setComment('€')
            ->setWidth(50);
    }

    public function getPlaceholders(Vpc_Shop_Cart_Order $order)
    {
        $remainingAmount = (float)$order->voucher_remaining_amount;
        return array(
            'voucherRemainingAmount' => Vps_View_Helper_Money::money($remainingAmount)
        );
    }
}
