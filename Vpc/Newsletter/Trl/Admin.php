<?php
class Vpc_Newsletter_Trl_Admin extends Vpc_Chained_Trl_MasterAsChild_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);

        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        foreach ($components as $component) {
            $c = $component;
            $language = '';
            while ($c = $c->parent) {
                if (Vpc_Abstract::getFlag($c->componentClass, 'hasLanguage'))
                    $language = $c->name;
            }
            $c = $component->getChildComponent('-child');
            $resource = new Vps_Acl_Resource_Component_MenuUrl($c);
            $config = $resource->getMenuConfig();
            $config['text'] .= " ($language)";
            $resource->setMenuConfig($config);
            $acl->add($resource, 'vpc_newsletter');
        }
    }
}
