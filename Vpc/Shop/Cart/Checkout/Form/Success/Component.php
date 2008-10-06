<?php
class Vpc_Shop_Cart_Checkout_Form_Success_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $t = new Vpc_Shop_Cart_Orders();
        $ret['order'] = $t->getCartOrder();
        $t = new Vpc_Shop_Cart_Checkout_Model();
        $ret['orderData'] = $t->find($ret['order']->id)->current();
        $ret['orderProducts'] = $ret['order']->findDependentRowset('Vpc_Shop_Cart_OrderProducts');

        $ret['subtotal'] = 0;
        foreach ($ret['orderProducts'] as $op) {
            $p = $op->findParentRow('Vpc_Shop_Products');
            $ret['subtotal'] += $p->price * $op->amount;
        }

        $ret['shipping'] = $this->_getShipping();

        $ret['total'] = $ret['subtotal'] + $ret['shipping'];

        $ret['confirm'] = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Shop_Cart_Component')
            ->getChildComponent('_checkout')
            ->getChildComponent('_confirm');

        return $ret;
    }

    protected function _getShipping()
    {
        return 3;
    }
}
