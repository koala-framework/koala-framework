<?php
class Vps_Acl_Component extends Vps_Acl
{
    public function __construct()
    {
        parent::__construct();

        $this->addRole(new Vps_Acl_Role('superuser', trlVps('Superuser')));
        $this->add(new Vps_Acl_Resource_EditRole('edit_role_superuser', 'superuser'), 'edit_role');

        $this->add(new Zend_Acl_Resource('vps_debug_class-tree'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_component_web'));
        $this->add(new Zend_Acl_Resource('vps_component_media'));
        $this->add(new Zend_Acl_Resource('vps_component_index'));
        $this->add(new Zend_Acl_Resource('vps_component_benchmark'));
        $this->add(new Zend_Acl_Resource('vps_component_show-component'));
        $this->add(new Vps_Acl_Resource_MenuUrl('vps_component_pages',
            array('text'=>trlVps('Sitetree'), 'icon'=>'application_side_tree.png'),
            '/admin/component/pages'));
            $this->add(new Zend_Acl_Resource('vps_component_components'),
                                'vps_component_pages'); // für /component/show
            $this->add(new Zend_Acl_Resource('vps_component'),
                                'vps_component_pages'); // für /component/edit

        $this->add(new Zend_Acl_Resource('vps_component_root')); //Komponenten können hier resourcen anhängen

        $this->allow(null, 'vps_component_web');
        $this->allow(null, 'vps_component_media');
        $this->allow(null, 'vps_component_index');
        $this->allow('admin', 'vps_component');
        $this->allow('superuser', 'vps_component');
        $this->allow('superuser', 'edit_role_superuser');

        $this->allow('admin', 'vps_component_show-component');
        $this->allow('admin', 'vps_component_pages');
        $this->allow('admin', 'vps_component_benchmark');
        $this->allow('superuser', 'vps_component_show-component');
        $this->allow('superuser', 'vps_component_pages');

        $this->allow(null, 'vps_component_root');

        $this->deny('guest', 'vps_component_index');
        $this->deny('guest', 'vps_component_pages');

        // Vps_Component_Acl nicht vergessen für Komponentenrechte!
    }
}
