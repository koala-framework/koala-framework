<?php
class Vpc_Form_Dynamic_Admin extends Vpc_Abstract_Composite_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        $components = Vps_Component_Data_Root::getInstance()->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));

        if (!$acl->has('vps_enquiries_dropdown')) {
            $acl->addResource(
                new Vps_Acl_Resource_MenuDropdown(
                    'vps_enquiries_dropdown', array('text'=>trlVps('Enquiries'), 'icon'=>'email.png')
                )
            );
        }

        if ($acl->has('vps_enquiries_enquiries')) {
            if (!$acl->inherits('vps_enquiries_enquiries', 'vps_enquiries_dropdown')) {
                $all = $acl->get('vps_enquiries_enquiries');
                $mc = $all->getMenuConfig();
                $mc['text'] = trlVps('All Enquiries');
                $all->setMenuConfig($mc);
                $acl->remove($all);
                $acl->addResource($all, 'vps_enquiries_dropdown');
            }
        }

        $name = Vpc_Abstract::getSetting($this->_class, 'componentName');
        if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        foreach ($components as $c) {
            $t = $c->getTitle();
            if (!$t) $t = $c->getPage()->name;
            $t = $name .' ('.$t.')';
            $menuUrl = Vpc_Admin::getInstance($c->componentClass)
                ->getControllerUrl('Enquiries') . '?componentId=' . $c->dbId;
            $acl->addResource(
                new Vps_Acl_Resource_Component_MenuUrl(
                    $c, array('text'=>$t, 'icon'=>$icon), $menuUrl
                ), 'vps_enquiries_dropdown'
            );
        }
    }
}