<?php
class Vpc_Shop_Products_Directory_Admin extends Vpc_Directories_Item_Directory_Admin
{
    protected function _getContentClass()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        return Vpc_Abstract::getChildComponentClass($detail, 'child', 'content');
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();
        $ret['items']['idTemplate'] = 'shopProducts_{0}-content';

        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        $ret['items']['componentPlugins'] = $this->_getChildComponentPlugins(array($detail, $this->_class));

        return $ret;
    }

    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        //TODO: ressource nur hinzufügen wenn es mindestens eine shop komponente im seitenbaum gibt
        $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_shop',
                    array('text'=>trlVps('Shop'), 'icon'=>'cart.png')), 'vps_component_root');
            $acl->add(new Vps_Acl_Resource_ComponentClass_MenuUrl($this->_class,
                    array('text'=>trlVps('Products'), 'icon'=>'application_view_list.png'),
                    Vpc_Admin::getInstance($this->_class)->getControllerUrl()), 'vpc_shop');
    }
}
