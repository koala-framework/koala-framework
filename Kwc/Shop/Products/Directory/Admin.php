<?php
class Kwc_Shop_Products_Directory_Admin extends Kwc_Directories_Item_Directory_Admin
{
    protected function _getPluginParentComponents()
    {
        $detail = Kwc_Abstract::getChildComponentClass($this->_class, 'detail');
        return array($detail, $this->_class);
    }
/*
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        //TODO: ressource nur hinzufügen wenn es mindestens eine shop komponente im seitenbaum gibt
        if (!$acl->has('kwc_shop')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_shop',
                    array('text'=>trlKwfStatic('Shop'), 'icon'=>'cart.png')), 'kwf_component_root');
        }
            $acl->add(new Kwf_Acl_Resource_ComponentClass_MenuUrl($this->_class,
                    array('text'=>trlKwfStatic('Products'), 'icon'=>'application_view_list.png'),
                    $this->getControllerUrl()), 'kwc_shop');
            foreach ($this->_getPluginAdmins() as $pluginAdmin) {
                $c = $pluginAdmin->_class;
                $acl->add(new Kwf_Acl_Resource_ComponentClass($c), 'kwc_'.$this->_class);
            }
    }
*/
}
