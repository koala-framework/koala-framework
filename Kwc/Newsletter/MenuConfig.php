<?php
class Kwc_Newsletter_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    protected function _getParentResource(Kwf_Acl $acl, $type)
    {
        if (!$acl->has('kwc_newsletter')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_newsletter', array(
                'text' => trlKwfStatic('Newsletter'),
                'icon' => 'email_open_image.png')
            ), 'kwf_component_root');
        }
        return 'kwc_newsletter';
    }

    protected function _getMenuConfigText(Kwf_Component_Data $c, $type) {
        if ($type == 'newsletter') {
            $componentName = Kwc_Abstract::getSetting($this->_class, 'componentName');
            return trlKwfStatic('Edit {0}', $componentName);
        }
    }

    public function addResources(Kwf_Acl $acl)
    {
        $icon = Kwc_Abstract::getSetting($this->_class, 'componentIcon');
        $menuConfig = array('icon'=>$icon);

        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        foreach ($components as $c) {
            $menuConfig['text'] = $this->_getMenuConfigText($c, 'newsletter');
            if (count($components) > 1) {
                $subRoot = $c;
                while($subRoot = $subRoot->parent) {
                    if (Kwc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
                }
                if ($subRoot) {
                    $menuConfig['text'] .= ' ('.$subRoot->name.')';
                }
            }
            $acl->add(
                new Kwf_Acl_Resource_Component_MenuUrl($c, $menuConfig),
                $this->_getParentResource($acl, 'newsletter')
            );
        }
    }

    public function getEventsClass()
    {
        return 'Kwf_Component_Abstract_MenuConfig_SameClass_Events';
    }
}
