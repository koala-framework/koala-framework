<?php
class Kwc_Shop_Cart_Component extends Kwc_Directories_Item_Directory_Component
{
    private $_chartPlugins;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['form'] = 'Kwc_Shop_Cart_Form_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_Shop_Cart_View_Component';
        $ret['generators']['detail']['class'] = 'Kwc_Shop_Cart_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Shop_Cart_Detail_Component';
        $ret['childModel'] = 'Kwc_Shop_Cart_OrderProducts';
        $ret['generators']['checkout'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Shop_Cart_Checkout_Component',
            'name' => trlKwf('Checkout')
        );
        $ret['viewCache'] = false;
        $ret['cssClass'] = 'webStandard webForm';
        $ret['componentName'] = trlKwfStatic('Shop.Cart');
        $ret['placeholder']['backToShop'] = trlKwfStatic('Back to shop');
        $ret['placeholder']['checkout'] = trlKwfStatic('To checkout');
        $ret['placeholder']['headline'] = trlKwfStatic('Your cart contains');

        $ret['assets']['files'][] = 'kwf/Kwc/Shop/Cart/Keepalive.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['assets']['dep'][] = 'ExtConnection';

        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['contentSender'] = 'Kwc_Shop_Cart_ContentSender';
        $ret['orderData'] = 'Kwc_Shop_Cart_OrderData';

        $ret['vatRate'] = 0.2;
        $ret['vatRateShipping'] = 0.2;
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
        return Kwc_Shop_Cart_OrderData::getInstance($this->getData()->componentClass)
                    ->getShopCartPlugins();
    }
}
