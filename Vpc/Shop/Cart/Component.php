<?php
class Vpc_Shop_Cart_Component extends Vpc_Directories_Item_Directory_Component
{
    private $_chartPlugins;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Vpc_Shop_Cart_Form_Component';
        $ret['generators']['child']['component']['view'] = 'Vpc_Shop_Cart_View_Component';
        $ret['generators']['detail']['class'] = 'Vpc_Shop_Cart_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_Shop_Cart_Detail_Component';
        $ret['childModel'] = 'Vpc_Shop_Cart_OrderProducts';
        $ret['generators']['checkout'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Shop_Cart_Checkout_Component',
            'name' => trlVps('Checkout')
        );
        $ret['viewCache'] = false;
        $ret['cssClass'] = 'webStandard webForm';
        $ret['componentName'] = trlVps('Shop.Cart');
        $ret['placeholder']['backToShop'] = trlVps('Back to shop');
        $ret['placeholder']['checkout'] = trlVps('To checkout');

        $ret['assets']['files'][] = 'vps/Vpc/Shop/Cart/Keepalive.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'ExtConnection';

        $ret['orderData'] = 'Vpc_Shop_Cart_OrderData';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['countProducts'] = $this->getData()->countChildComponents(array('generator'=>'detail'));
        $ret['checkout'] = $this->getData()->getChildComponent('_checkout');
        return $ret;
    }

    public final function getShopCartPlugins()
    {
        return Vpc_Shop_Cart_OrderData::getInstance($this->getData()->componentClass)
                    ->getShopCartPlugins();
    }
}
