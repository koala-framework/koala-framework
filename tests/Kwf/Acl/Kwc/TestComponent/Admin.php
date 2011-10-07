<?php
class Kwf_Acl_Kwc_TestComponent_Admin extends Kwc_Admin
{
    public function addResources(Kwf_Acl $acl)
    {
        parent::addResources($acl);
        $acl->add(new Kwf_Acl_Resource_MenuUrl('misc_languages',
                array('text'=>'Sprachen', 'icon'=>'comment.png'),
                '/admin/misc/languages'), 'misc');
    }
}
