<?php
class Kwc_Shop_Products_Directory_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);

        $ret['childModel'] = 'Kwc_Shop_Products_Directory_Trl_Model';

        $ret['menuConfig'] = 'Kwf_Component_Abstract_MenuConfig_Trl_SameClass';

        //darf im seitenbaum nicht berbeitet werden
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';

        //config fuer admin button oben
        $ret['extConfigControllerIndex'] = 'Kwc_Directories_Item_Directory_ExtConfigEditButtons';
        
        $ret['generators']['addToCart']['class'] = 'Kwc_Shop_Products_Directory_Trl_AddToCartGenerator';

        return $ret;
    }
}
