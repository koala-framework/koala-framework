<?php
class Vps_Acl_Component extends Vps_Acl
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new Zend_Acl_Resource('vps_debug_classtree'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_component_web'));
        $this->add(new Zend_Acl_Resource('vps_component_media'));
        $this->add(new Zend_Acl_Resource('vps_component_index'));
        $this->add(new Zend_Acl_Resource('vps_component_benchmark'));
        $this->add(new Zend_Acl_Resource('vps_component_showcomponent'));
        $this->add(new Vps_Acl_Resource_MenuUrl('vps_component_pages',
            array('text'=>trlVps('Sitetree'), 'icon'=>'application_side_tree.png'),
            '/admin/component/pages'));
            $this->add(new Zend_Acl_Resource('vps_component_pageedit'), 'vps_component_pages');
            $this->add(new Zend_Acl_Resource('vps_component_components'),
                                'vps_component_pages'); // für /component/show
            $this->add(new Zend_Acl_Resource('vps_component'),
                                'vps_component_pages'); // für /component/edit

        $this->add(new Zend_Acl_Resource('vps_component_root')); //Komponenten können hier resourcen anhängen

        $this->allow(null, 'vps_component_web');
        $this->allow(null, 'vps_component_media');
        $this->allow(null, 'vps_component_index');
        $this->allow('admin', 'vps_component');

        $this->allow('admin', 'vps_component_showcomponent');
        $this->allow('admin', 'vps_component_pages');
        $this->allow('admin', 'vps_component_benchmark');

        $this->allow(null, 'vps_component_root');

        $this->deny('guest', 'vps_component_index');
        $this->deny('guest', 'vps_component_pages');
    }
}
