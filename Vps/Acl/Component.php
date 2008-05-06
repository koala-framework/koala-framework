<?php
class Vps_Acl_Component extends Vps_Acl
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new Zend_Acl_Resource('vps_debug_treecache'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_component_web'));
        $this->add(new Zend_Acl_Resource('vps_component_media'));
        $this->add(new Zend_Acl_Resource('vps_component_index'));
        $this->add(new Vps_Acl_Resource_MenuUrl('vps_component_pages',
            array('text'=>trlVps('Sitetree'), 'icon'=>'application_side_tree.png'),
            '/admin/component/pages'));
            $this->add(new Zend_Acl_Resource('vps_component_pageedit'), 'vps_component_pages');
            $this->add(new Zend_Acl_Resource('vps_component_components'),
                                'vps_component_pages'); // für /component/show
            $this->add(new Zend_Acl_Resource('vps_component'),
                                'vps_component_pages'); // für /component/edit

        $this->add(new Vps_Acl_Resource_MenuUrl('vps_component_overview',
            array('text'=>trlVps('Components'), 'icon'=>'application_view_list.png'),
            '/admin/component/overview'));

        $this->allow(null, 'vps_component_web');
        $this->allow(null, 'vps_component_media');
        $this->allow(null, 'vps_component_index');
        $this->allow(null, 'vps_component');

        $this->allow('admin', 'vps_component_overview');
        $this->allow('admin', 'vps_component_pages');

        $this->deny('guest', 'vps_component_index');
        $this->deny('guest', 'vps_component_pages');

        $this->add(new Zend_Acl_Resource('vps_cli_tc'));
        $this->add(new Zend_Acl_Resource('vps_cli_textcomponents'));
        $this->allow('cli', 'vps_cli_tc');
        $this->allow('cli', 'vps_cli_textcomponents');

    }
}
