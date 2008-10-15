<?php
class Vpc_Shop_Cart_Checkout_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Shop_Cart_Checkout_Form_Component';
        $ret['generators']['confirm'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Shop_Cart_Checkout_Confirm_Component',
            'name' => trlVps('Send order')
        );
        $ret['cssClass'] = 'webForm webStandard';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['keys'] = array();
        foreach ($this->getData()->getChildComponents(array('generator' => 'child')) as $c) {
            $ret[$c->id] = $c;
            $ret['keys'][] = $c->id;
        }
        return $ret;
    }
}
