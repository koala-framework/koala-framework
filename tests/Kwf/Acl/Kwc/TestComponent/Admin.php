<?php
class Vps_Acl_Vpc_TestComponent_Admin extends Vpc_Admin
{
    public function addResources(Vps_Acl $acl)
    {
        parent::addResources($acl);
        $acl->add(new Vps_Acl_Resource_MenuUrl('misc_languages',
                array('text'=>'Sprachen', 'icon'=>'comment.png'),
                '/admin/misc/languages'), 'misc');
    }
}
