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

    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_shop',
                    array('text'=>trlVps('Shop'), 'icon'=>'cart.png')), 'vps_component_root');
            $acl->add(new Vps_Acl_Resource_ComponentClass_MenuUrl($this->_class,
                    array('text'=>trlVps('Products'), 'icon'=>'application_view_list.png'),
                    Vpc_Admin::getInstance($this->_class)->getControllerUrl()), 'vpc_shop');
    }
}
