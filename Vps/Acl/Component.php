<?php
class Vps_Acl_Component extends Vps_Acl
{
    public function __construct()
    {
        parent::__construct();

        $this->addRole(new Vps_Acl_Role('admin', 'Admin'));

        $this->add(new Zend_Acl_Resource('vps_web'));
        $this->add(new Zend_Acl_Resource('vps_media'));
        $this->add(new Zend_Acl_Resource('vps_index'));
        $this->add(new Vps_Acl_Resource_MenuUrl('vps_pages',
            array('text'=>'Sitetree', 'icon'=>'application_side_tree.png'),
            '/admin/component/pages'));
            $this->add(new Zend_Acl_Resource('vps_pageedit'), 'vps_pages');
            $this->add(new Zend_Acl_Resource('vps_components'), 'vps_pages'); // für /component/show
            $this->add(new Zend_Acl_Resource('vps_component'), 'vps_pages'); // für /component/edit

        $this->add(new Vps_Acl_Resource_MenuUrl('vps_overview',
            array('text'=>'Components', 'icon'=>'application_view_list.png'),
            '/admin/component/overview'));

        $this->add(new Vps_Acl_Resource_MenuUrl('vps_user',
            array('text'=>'User', 'icon'=>'folder_user.png'),
            '/admin/component/user'));
            $this->add(new Zend_Acl_Resource('vps_useredit'), 'vps_user');
            
        $this->allow(null, 'vps_web');
        $this->allow(null, 'vps_media');

        $this->allow('admin', 'vps_index');
        $this->allow('admin', 'vps_pages');
        $this->allow('admin', 'vps_overview');
        $this->allow('admin', 'vps_user');
    }
}
