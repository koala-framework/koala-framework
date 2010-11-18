<?php
class Vpc_Guestbook_Admin extends Vpc_Directories_Item_Directory_Admin
{
    protected function _getContentClass()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        return Vpc_Abstract::getChildComponentClass($detail, 'child', 'content');
    }

    public function getExtConfig()
    {
        $ret = parent::getExtConfig();

        $url = Vpc_Admin::getInstance($this->_class)->getControllerUrl('Settings');
        $icon = new Vps_Asset('wrench_orange');
        $arr = array('settings' => array(
            'xtype' => 'vps.autoform',
            'controllerUrl' => $url,
            'title' => trlVps('Guestbook Settings'),
            'icon' => $icon->__toString()
        ));
        return array_merge($arr, $ret);
    }

    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        if (!$components) return;
        $name = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);
        $c = $components[0];

        if (!$acl->has('guestbook_entries')) {
            $acl->add(new Vps_Acl_Resource_MenuUrl('guestbook_entries',
                array('text'=>$name, 'icon'=>$icon),
                Vpc_Admin::getInstance($c->componentClass)->getControllerUrl('Comments').'?componentId='.$c->dbId), 'vps_component_root');
        }

    }
}
