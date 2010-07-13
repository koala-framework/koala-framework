<?php
class Vpc_Newsletter_Trl_Admin extends Vpc_Chained_Trl_MasterAsChild_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);

        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $name = Vpc_Abstract::getSetting($this->_class, 'componentName');
        $icon = Vpc_Abstract::getSetting($this->_class, 'componentIcon');
        if (strpos($name, '.') !== false) $name = substr($name, strrpos($name, '.') + 1);

        foreach ($components as $component) {
            $c = $component;
            $language = '';
            while ($c = $c->parent) {
                if (Vpc_Abstract::getFlag($c->componentClass, 'hasLanguage'))
                    $language = $c->name;
            }
            $c = $component->getChildComponent('-child');
            $acl->add(new Vps_Acl_Resource_Component_MenuUrl($c,
                array('text'=>trlVps('Edit {0}', $name . ' (' . $language . ')'), 'icon'=>$icon),
                Vpc_Admin::getInstance($c->componentClass)->getControllerUrl().'?componentId='.$c->dbId),
            'vpc_newsletter');
        }
    }
}
