<?php
/**
 * Standard Acl für IsiWeb-Artige Webs mit ein paar Content-Seiten und einem
 * Kontakt Formular.
 *
 * Aufwändigere Webs sollten Vps_Acl_Component verwenden.
 */
class Vps_Acl_Component_IsiWeb extends Vps_Acl_Component
{
    public function __construct()
    {
        parent::__construct();

        $this->add(new Vps_Acl_Resource_MenuUrl('vps_enquiries_enquiries',
                array('text'=>'Anfragen', 'icon'=>'email.png'),
                '/vps/enquiries/enquiries'));

        $this->add(new Vps_Acl_Resource_MenuDropdown('settings',
                    array('text'=>trl('Einstellungen'), 'icon'=>'wrench.png')));
            $this->add(new Vps_Acl_Resource_MenuUrl('vps_user_users',
                    array('text'=>trl('Benutzerverwaltung'), 'icon'=>'user.png'),
                    '/vps/user/users'), 'settings');
                $this->add(new Zend_Acl_Resource('vps_user_user'), 'vps_user_users');
                $this->add(new Zend_Acl_Resource('vps_user_log'), 'vps_user_users');
                $this->add(new Zend_Acl_Resource('vps_user_comments'), 'vps_user_users');

        $this->allow('admin', null);
        $this->allow('superuser', 'settings');
        $this->allow('superuser', 'vps_enquiries_enquiries');
    }
}
