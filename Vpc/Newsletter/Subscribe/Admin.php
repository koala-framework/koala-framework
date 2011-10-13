<?php
class Vpc_Newsletter_Subscribe_Admin_Resouce extends Vps_Acl_Resource_ComponentClass_MenuUrl
    implements Vps_Acl_Resource_Component_Interface
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

class Vpc_Newsletter_Subscribe_Admin extends Vpc_Abstract_Composite_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);

        if (!$acl->has('vpc_newsletter')) {
            $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_newsletter',
                array('text'=>trlVps('Newsletter'), 'icon'=>'email_open_image.png')), 'vps_component_root');
        }

        $menuConfig = array('icon'=>new Vps_Asset('group.png'));
        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsByClass('Vpc_Newsletter_Component', array('ignoreVisible'=>true));
        foreach ($components as $c) {
            $menuConfig['text'] = trlVps('Recipients');
            if (count($components) > 1) {
                $subRoot = $c;
                while($subRoot = $subRoot->parent) {
                    if (Vpc_Abstract::getFlag($subRoot->componentClass, 'subroot')) break;
                }
                if ($subRoot) {
                    $menuConfig['text'] .= ' ('.$subRoot->name.')';
                }
            }
            $acl->add(new Vpc_Newsletter_Subscribe_Admin_Resouce($this->_class.$c->dbId,
                $menuConfig,
                $this->getControllerUrl('Recipients').'?newsletterComponentId='.$c->dbId,
                $this->_class, $c),
            'vpc_newsletter');
        }
    }
}
