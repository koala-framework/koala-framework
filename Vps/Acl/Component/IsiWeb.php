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
                array('text'=>trlVps('Enquiries'), 'icon'=>'email.png'),
                '/vps/enquiries/enquiries'));

        $this->add(new Vps_Acl_Resource_MenuDropdown('settings',
                    array('text'=>trlVps('Toolbox'), 'icon'=>'wrench.png')));
            $this->add(new Vps_Acl_Resource_MenuUrl('vps_user_users',
                    array('text'=>trlVps('Useradministration'), 'icon'=>'user.png'),
                    '/vps/user/users'), 'settings');
                $this->add(new Zend_Acl_Resource('vps_user_user'), 'vps_user_users');
                $this->add(new Zend_Acl_Resource('vps_user_log'), 'vps_user_users');
                $this->add(new Zend_Acl_Resource('vps_user_comments'), 'vps_user_users');
            $this->add(new Vps_Acl_Resource_MenuUrl('vps_project-timer_timer',
                    array('text'=>trlVps('Time recording'), 'icon'=>'clock.png'),
                    '/vps/project-timer/timer'), 'settings');
                $this->add(new Zend_Acl_Resource('vps_project-timer_years'), 'vps_project-timer_timer');
            $this->add(new Vps_Acl_Resource_MenuUrl('vps_component_clear-cache',
                    array('text'=>trlVps('Clear Cache'), 'icon'=>'database.png'),
                    '/admin/component/clear-cache'), 'settings');
            $this->add(new Vps_Acl_Resource_MenuUrl('vps_redirects_redirects',
                    array('text'=>trlVps('Redirects'), 'icon'=>'page_white_go.png'),
                    '/vps/redirects/redirects'), 'settings');
                $this->add(new Zend_Acl_Resource('vps_redirects_redirect'), 'vps_redirects_redirects');
                    $this->add(new Zend_Acl_Resource('vps_redirects_pages'), 'vps_redirects_redirect');


        $this->allow('admin', null);
        $this->allow('superuser', 'settings');
        $this->allow('superuser', 'vps_enquiries_enquiries');
        $this->deny('superuser', 'vps_component_clear-cache');
    }
}
