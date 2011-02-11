<?php
class Vpc_NewsletterCategory_Admin extends Vpc_Newsletter_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        if (!$acl->has('vpc_newsletter')) {
            $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_newsletter',
                array('text'=>trlVps('Newsletter'), 'icon'=>'email_open_image.png')), 'vps_component_root');
        }

        $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $c = $components[0];
        $icon = new Vps_Asset('package');

        $acl->add(
            new Vps_Acl_Resource_ComponentClass_MenuUrl(
                $c->componentClass,
                array('text'=>trlVps('Edit {0}', trlVps('Categories')), 'icon'=>$icon),
                $this->getControllerUrl('Categories').'?componentId='.$c->dbId
            ),
            'vpc_newsletter'
        );
        parent::addResources($acl);
    }
}
