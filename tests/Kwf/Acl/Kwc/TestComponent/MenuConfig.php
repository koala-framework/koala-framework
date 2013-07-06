<?php
class Kwf_Acl_Kwc_TestComponent_MenuConfig extends Kwf_Component_Abstract_MenuConfig_Abstract
{
    public function addResources(Kwf_Acl $acl)
    {
        $acl->add(new Kwf_Acl_Resource_MenuUrl('misc_languages',
                array('text'=>'Sprachen', 'icon'=>'comment.png'),
                '/admin/misc/languages'), 'misc');
    }
}
