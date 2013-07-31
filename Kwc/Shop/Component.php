<?php
class Kwc_Shop_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child']['component']['products'] = 'Kwc_Shop_Products_Directory_Component';

        $ret['generators']['cart'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Shop_Cart_Component',
            'name' => trlKwfStatic('Cart'),
            'showInMenu' => true
        );

        $ret['componentName'] = trlKwfStatic('Shop');
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }
}
