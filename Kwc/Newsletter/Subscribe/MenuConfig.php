<?php
class Kwc_Newsletter_Subscribe_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
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

    protected function _getMenuConfigText(Kwf_Component_Data $c) {
        return trlKwf('Recipients');
    }

    public function addResources(Kwf_Acl $acl)
    {
        $menuConfig = array('icon'=>new Kwf_Asset('group.png'));
        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsByClass(
                    Kwc_Abstract::getSetting($this->_class, 'subscribeToNewsletterClass'),
                    array('ignoreVisible'=>true)
                );
        foreach ($components as $c) {
            $menuConfig['text'] = $this->_getMenuConfigText($c);
            if (count($components) > 1) {
                $subRoot = $c;
                while($subRoot = $subRoot->parent) {
                    if (Kwc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
                }
                if ($subRoot) {
                    $menuConfig['text'] .= ' ('.$subRoot->name.')';
                }
            }
            $acl->add(new Kwc_Newsletter_Subscribe_Resource($this->_class.$c->dbId,
                $menuConfig,
                Kwc_Admin::getInstance($this->_class)->getControllerUrl('Recipients').'?newsletterComponentId='.$c->dbId,
                $this->_class, $c),
                $this->_getParentResource($acl, 'subscribe')
            );
        }
    }
    public function getEventsClass()
    {
        return 'Kwc_Newsletter_Subscribe_MenuConfigEvents';
    }
}
