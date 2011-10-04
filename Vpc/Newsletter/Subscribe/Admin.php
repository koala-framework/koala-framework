<?php
class Vpc_Newsletter_Subscribe_Admin extends Vpc_Abstract_Composite_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);

        if (!$acl->has('vpc_newsletter')) {
            $acl->add(new Vps_Acl_Resource_MenuDropdown('vpc_newsletter',
                array('text'=>trlVps('Newsletter'), 'icon'=>'email_open_image.png')), 'vps_component_root');
        }

        $acl->add(new Vps_Acl_Resource_ComponentClass_MenuUrl($this->_class,
            array('text'=>trlVps('Recipients'), 'icon'=>new Vps_Asset('group.png')),
            $this->getControllerUrl('Recipients')),
        'vpc_newsletter');
    }
}
