<?php
class Vpc_Shop_Cart_Checkout_Payment_Abstract_Mail_Paragraphs_Products_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['componentName'] = trlVps('Order Products');
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $data = $this->getData()->getParentByClass('Vpc_Mail_Component')->getComponent()->getMailData();
        if ($data) {
            $items = $data['order']->getChildRows('Products');
            $ret['items'] = array();
            foreach ($items as $i) {
                $ret['items'][] = (object)array(
                    'product' => Vps_Component_Data_Root::getInstance()
                                    ->getComponentByDbId($i->add_component_id)
                                    ->parent,
                    'row' => $i
                );
            }
            $ret['sumRows'] = $data['sumRows'];
        }

        return $ret;
    }

}
