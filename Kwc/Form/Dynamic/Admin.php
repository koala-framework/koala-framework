<?php
class Kwc_Form_Dynamic_Admin extends Kwc_Abstract_Composite_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        $components = Kwf_Component_Data_Root::getInstance()->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));

        if (!$acl->has('kwf_enquiries_dropdown')) {
            $acl->addResource(
                new Kwf_Acl_Resource_MenuDropdown(
                    'kwf_enquiries_dropdown', array('text'=>trlKwf('Enquiries'), 'icon'=>'email.png')
                )
            );
        }

        if ($acl->has('kwf_enquiries_enquiries')) {
            if (!$acl->inherits('kwf_enquiries_enquiries', 'kwf_enquiries_dropdown')) {
                $all = $acl->get('kwf_enquiries_enquiries');
                $mc = $all->getMenuConfig();
                $mc['text'] = trlKwf('All Enquiries');
                $all->setMenuConfig($mc);
                $acl->remove($all);
                $acl->addResource($all, 'kwf_enquiries_dropdown');
            }
        }

        $name = Kwc_Abstract::getSetting($this->_class, 'componentName');
        if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        foreach ($components as $c) {
            $t = $c->getTitle();
            if (!$t) $t = $c->getPage()->name;
            $t = $name .' ('.$t.')';
            $menuUrl = Kwc_Admin::getInstance($c->componentClass)
                ->getControllerUrl('Enquiries') . '?componentId=' . $c->dbId;
            $acl->addResource(
                new Kwf_Acl_Resource_Component_MenuUrl(
                    $c, array('text'=>$t, 'icon'=>$icon), $menuUrl
                ), 'kwf_enquiries_dropdown'
            );
        }
    }
}