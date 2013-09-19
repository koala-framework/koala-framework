<?php
class Kwf_Controller_Router extends Zend_Controller_Router_Rewrite
{
    public function __construct($prefix)
    {
        parent::__construct();
        $this->AddRoute('kwf_welcome', new Zend_Controller_Router_Route(
                    '/kwf/welcome/:controller/:action',
                    array('module'     => 'kwf_controller_action_welcome',
                          'controller' =>'index',
                          'action'     =>'index')));
        $this->AddRoute('kwf_user', new Zend_Controller_Router_Route(
                    '/kwf/user/:controller/:action',
                    array('module'     => 'kwf_controller_action_user',
                          'action'     =>'index')));
        $this->AddRoute('kwf_error', new Zend_Controller_Router_Route(
                    '/kwf/error/:controller/:action',
                    array('module'     => 'kwf_controller_action_error')));
        $this->AddRoute('kwf_start', new Zend_Controller_Router_Route(
                    '/kwf/start',
                    array('module'      => 'kwf_controller_action_welcome',
                          'controller'  => 'start',
                          'action'      => 'index')));
        $this->AddRoute('kwf_pool', new Zend_Controller_Router_Route(
                    '/kwf/pool/:controller/:action',
                    array('module'     => 'kwf_controller_action_pool',
                          'controller' => 'pools',
                          'action'     => 'index')));
        $this->AddRoute('kwf_trl', new Zend_Controller_Router_Route(
                    '/kwf/trl/:controller/:action',
                    array('module'     => 'kwf_controller_action_trl',
                          'controller' => 'index',
                          'action'     => 'index')));
        $this->AddRoute('kwf_debug', new Zend_Controller_Router_Route(
                    '/kwf/debug/:controller/:action',
                    array('module'     => 'kwf_controller_action_debug',
                          'controller' => 'index',
                          'action'     => 'index')));
        $this->AddRoute('kwf_media', new Zend_Controller_Router_Route(
                    '/kwf/media/:controller/:action',
                    array('module'     => 'kwf_controller_action_media',
                          'controller' => 'index',
                          'action'     => 'index')));
        $this->AddRoute('kwf_spam', new Zend_Controller_Router_Route(
                    '/kwf/spam/:controller/:action',
                    array('module'     => 'kwf_controller_action_spam',
                          'action'     =>'index')));
        $this->AddRoute('kwf_enquiries', new Zend_Controller_Router_Route(
                    '/kwf/enquiries/:controller/:action',
                    array('module'     => 'kwf_controller_action_enquiries',
                          'action'     =>'index')));
        $this->AddRoute('kwf_redirects', new Zend_Controller_Router_Route(
                    '/kwf/redirects/:controller/:action',
                    array('module'     => 'kwf_controller_action_redirects',
                          'action'     =>'index')));
        $this->AddRoute('kwf_util', new Zend_Controller_Router_Route(
                    '/kwf/util/:controller/:action',
                    array('module'     => 'kwf_controller_action_util',
                          'action'     =>'index')));
        $this->AddRoute('kwf_maintenance', new Zend_Controller_Router_Route(
                    '/kwf/maintenance/:controller/:action',
                    array('module'     => 'kwf_controller_action_maintenance',
                          'action'     =>'index')));
        $this->AddRoute('kwf_component', new Zend_Controller_Router_Route(
                    '/kwf/component/:controller/:action',
                    array('module'     => 'kwf_controller_action_component',
                          'action'     =>'index')));

        if (Kwf_Registry::get('config')->includepath->kwfTests) {
            //für selenium-tests von sachen die im kwf liegen
            $this->AddRoute('kwf_test', new Zend_Controller_Router_Route(
                        '/kwf/test/:controller/:action',
                        array('module'     => 'kwf_test',
                            'action'     =>'index')));
            $this->AddRoute('kwf_kwctest', new Zend_Controller_Router_Route_Regex(
                        'kwf/kwctest/([^/]+)/(.*)',
                        array('module'     => 'kwf_test',
                            'controller' => 'kwc_test',
                            'action'     => 'index',
                            'url'        => ''),
                        array('root'=>1, 'url'=>2)));
            $this->AddRoute('kwf_test_componentedit', new Zend_Controller_Router_Route(
                        '/kwf/componentedittest/:root/:class/:componentController/:action',
                        array('module' => 'component_test',
                            'controller' => 'component_test',
                            'action' => 'index')));
        }

        if (Kwf_Registry::get('config')->includepath->webTests) {
            //für selenium-tests von sachen die im web liegen
            $this->AddRoute('web_test', new Zend_Controller_Router_Route(
                        '/kwf/webtest/:controller/:action',
                        array('module'     => 'web_test',
                            'action'     =>'index')));
        }

        //Komponenten routes
        if ($prefix) {
            $prefix = '/'.$prefix;
            $this->AddRoute('admin', new Zend_Controller_Router_Route(
                    $prefix.'/:module/:controller/:action',
                    array('module'=>'index',
                          'controller' => 'index',
                          'action' => 'index')));
            $this->AddRoute('welcome', new Zend_Controller_Router_Route(
                    $prefix.'',
                    array('module'=>'kwf_controller_action_welcome',
                          'controller' => 'welcome',
                          'action' => 'index')));
        }
        $this->AddRoute('component', new Zend_Controller_Router_Route(
                    $prefix.'/component/:controller/:action',
                    array('module'=>'kwf_controller_action_component',
                          'action' => 'index')));
        $this->AddRoute('components', new Zend_Controller_Router_Route(
                    $prefix.'/components/:action',
                    array('module' => 'kwf_controller_action_component',
                          'controller' => 'components',
                          'action' => 'index')));
        $this->AddRoute('componentshow', new Zend_Controller_Router_Route(
                    $prefix.'/component/show/:class/:componentId',
                    array('componentId'=>null,
                          'module' => 'kwf_controller_action_component',
                          'controller' => 'components',
                          'action' => 'show')));
        $this->AddRoute('componentedit', new Zend_Controller_Router_Route(
                    $prefix.'/component/edit/:class/:componentController/:action',
                    array('module' => 'component',
                          'controller' => 'component',
                          'action' => 'index')));
    }
}
