<?php
class Kwc_Newsletter_Trl_Admin extends Kwc_Chained_Trl_MasterAsChild_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);

        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        foreach ($components as $component) {
            $c = $component;
            $language = '';
            while ($c = $c->parent) {
                if (Kwc_Abstract::getFlag($c->componentClass, 'hasLanguage'))
                    $language = $c->name;
            }
            $c = $component->getChildComponent('-child');
            $resource = new Kwf_Acl_Resource_Component_MenuUrl($c);
            $config = $resource->getMenuConfig();
            $config['text'] .= " ($language)";
            $resource->setMenuConfig($config);
            $acl->add($resource, 'kwc_newsletter');
        }
    }
}
