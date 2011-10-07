<?php
class Kwc_NewsletterCategory_Admin extends Kwc_Newsletter_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        if (!$acl->has('kwc_newsletter')) {
            $acl->add(new Kwf_Acl_Resource_MenuDropdown('kwc_newsletter',
                array('text'=>trlKwf('Newsletter'), 'icon'=>'email_open_image.png')), 'kwf_component_root');
        }

        $components = Kwf_Component_Data_Root::getInstance()
                ->getComponentsBySameClass($this->_class, array('ignoreVisible'=>true));
        $c = $components[0];
        $icon = new Kwf_Asset('package');

        $acl->add(
            new Kwf_Acl_Resource_ComponentClass_MenuUrl(
                $c->componentClass,
                array('text'=>trlKwf('Edit {0}', trlKwf('Categories')), 'icon'=>$icon),
                $this->getControllerUrl('Categories').'?componentId='.$c->dbId
            ),
            'kwc_newsletter'
        );
        parent::addResources($acl);
    }
}
