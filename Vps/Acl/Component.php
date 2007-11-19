<?php
class Vps_Acl_Component extends Vps_Acl
{
    public function __construct()
    {
        parent::__construct();

        $this->addRole(new Vps_Acl_Role('admin', 'Admin'));

        $this->add(new Zend_Acl_Resource('web'));
        $this->add(new Zend_Acl_Resource('media'));
        $this->add(new Zend_Acl_Resource('mediaoriginal'));
        $this->add(new Zend_Acl_Resource('mediavpc'));
        //$this->add(new Zend_Acl_Resource('fe'));
        $this->add(new Vps_Acl_Resource_MenuUrl('pages',
            array('text'=>'Sitetree', 'icon'=>'application_side_tree.png'),
            '/admin/component/pages'));
            $this->add(new Zend_Acl_Resource('pageedit'), 'pages');
            $this->add(new Zend_Acl_Resource('components'), 'pages'); // für /component/show
            $this->add(new Zend_Acl_Resource('component'), 'pages'); // für /component/edit

        $this->add(new Vps_Acl_Resource_MenuUrl('overview',
            array('text'=>'Components', 'icon'=>'application_view_list.png'),
            '/admin/component/overview'));


        $this->allow(null, 'web');
        $this->allow(null, 'media');

        $this->allow('admin', 'pages');
        $this->allow('admin', 'mediaoriginal');
        $this->allow('admin', 'mediavpc');
        $this->allow('admin', 'overview');
    }
}
