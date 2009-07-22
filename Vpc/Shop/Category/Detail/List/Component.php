<?php
class Vpc_Shop_Category_Detail_List_Component extends Vpc_Directories_Category_Detail_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['view'] = 'Vpc_Shop_Products_View_Component';
        $ret['generators']['addToCart'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vpc_Shop_Products_Directory_AddToCart_Component',
            'model' => 'Vpc_Shop_Products'
        );
        return $ret;
    }
}
