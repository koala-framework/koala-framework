<?php
class Vpc_Shop_Box_Cart_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['cart'] = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Shop_Cart_Component');
        $ret['checkout'] = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Shop_Cart_Checkout_Component');
        $ret['items'] = $ret['cart']->getChildComponents(array('generator'=>'detail'));
        foreach ($ret['items'] as $i) {
            $i->product = Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId($i->row->add_component_id)
                ->parent;
        }
        $ret['viewCache'] = false;
        return $ret;
    }

}
