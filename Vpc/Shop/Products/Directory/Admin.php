<?php
class Vpc_Shop_Products_Directory_Admin extends Vpc_Directories_Item_Directory_Admin
{
    protected function _getContentClass()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        return Vpc_Abstract::getChildComponentClass($detail, 'child', 'content');
    }

    protected function _getPluginParentComponents()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        return array($detail, $this->_class);
    }

    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        //TODO: ressource nur hinzufügen wenn es mindestens eine shop komponente im seitenbaum gibt
        if (!$acl->has('vpc_shop')) {
            $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_shop',
                    array('text'=>trlVps('Shop'), 'icon'=>'cart.png')), 'vps_component_root');
        }
            $acl->add(new Vps_Acl_Resource_ComponentClass_MenuUrl($this->_class,
                    array('text'=>trlVps('Products'), 'icon'=>'application_view_list.png'),
                    $this->getControllerUrl()), 'vpc_shop');
            foreach ($this->_getPluginAdmins() as $pluginAdmin) {
                $c = $pluginAdmin->_class;
                $acl->add(new Vps_Acl_Resource_ComponentClass($c), 'vpc_'.$this->_class);
            }
    }
}
