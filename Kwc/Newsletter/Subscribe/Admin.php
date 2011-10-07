<?php
class Kwc_Newsletter_Subscribe_Admin extends Kwc_Abstract_Composite_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);

        if (!$acl->has('kwc_newsletter')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_newsletter',
                array('text'=>trlKwf('Newsletter'), 'icon'=>'email_open_image.png')), 'kwf_component_root');
        }

        $acl->add(new Kwf_Acl_Resource_ComponentClass_MenuUrl($this->_class,
            array('text'=>trlKwf('Recipients'), 'icon'=>new Kwf_Asset('group.png')),
            $this->getControllerUrl('Recipients')),
        'kwc_newsletter');
    }
}
