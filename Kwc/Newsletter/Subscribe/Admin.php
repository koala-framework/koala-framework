<?php
class Kwc_Newsletter_Subscribe_Admin_Resouce extends Kwf_Acl_Resource_ComponentClass_MenuUrl
    implements Kwf_Acl_Resource_Component_Interface
{
    private $_component;
    public function __construct($resourceId, $menuConfig, $menuUrl, $componentClass, $component)
    {
        parent::__construct($resourceId, $menuConfig, $menuUrl, $componentClass);
        $this->_component = $component;
    }
    public function getComponent() {
        return $this->_component;
    }
}

class Kwc_Newsletter_Subscribe_Admin extends Kwc_Abstract_Composite_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);

        if (!$acl->has('kwc_newsletter')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_newsletter',
                array('text'=>trlKwf('Newsletter'), 'icon'=>'email_open_image.png')), 'kwf_component_root');
        }

        $menuConfig = array('icon'=>new Kwf_Asset('group.png'));
        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsByClass('Kwc_Newsletter_Component', array('ignoreVisible'=>true));
        foreach ($components as $c) {
            $menuConfig['text'] = trlKwf('Recipients');
            if (count($components) > 1) {
                $subRoot = $c;
                while($subRoot = $subRoot->parent) {
                    if (Kwc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
                }
                if ($subRoot) {
                    $menuConfig['text'] .= ' ('.$subRoot->name.')';
                }
            }
            $acl->add(new Kwc_Newsletter_Subscribe_Admin_Resouce($this->_class.$c->dbId,
                $menuConfig,
                $this->getControllerUrl('Recipients').'?newsletterComponentId='.$c->dbId,
                $this->_class, $c),
            'kwc_newsletter');
        }
    }
}
