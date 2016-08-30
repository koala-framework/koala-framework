<?php
class Kwc_Shop_Category_Detail_List_Component extends Kwc_Directories_Category_Detail_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['view'] = 'Kwc_Shop_Products_View_Component';
        $ret['generators']['addToCart'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwc_Shop_Products_Directory_AddToCart_Component',
            'model' => 'Kwc_Shop_Products'
        );
        return $ret;
    }
}
