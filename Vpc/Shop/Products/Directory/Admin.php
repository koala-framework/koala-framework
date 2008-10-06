<?php
class Vpc_Shop_Products_Directory_Admin extends Vpc_Directories_Item_Directory_Admin
{
    public function getExtConfig()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        $generators = Vpc_Abstract::getSetting($detail, 'generators');
        $contentClass = $generators['child']['component']['content'];
    
        return array_merge(parent::getExtConfig(), array(
            'xtype'=>'vpc.shop.products',
            'contentClass' => $contentClass,
            'idTemplate' => 'shopProducts_{0}-content'
        ));
    }
}
