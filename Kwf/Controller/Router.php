<?php
class Kwf_Controller_Router extends Zend_Controller_Router_Rewrite
{
    protected $_useDefaultRoutes = false;

    public function __construct($prefix)
    {
        parent::__construct();

        $this->AddRoute('admin', new Zend_Controller_Router_Route(
                $prefix.'/:module/:controller/:action',
                array('module'     =>'index',
                      'controller' => 'index',
                      'action'     => 'index')));

        if (!$prefix) {
            $this->AddRoute('welcome', new Zend_Controller_Router_Route(
                '',
                array('module'=>'kwf_controller_action_welcome',
                    'controller' => 'welcome',
                    'action' => 'index')));
        }

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
        $this->AddRoute('kwf_component', new Zend_Controller_Router_Route(
                    '/kwf/component/:controller/:action',
                    array('module'     => 'kwf_controller_action_component',
                          'action'     =>'index')));

        $apiRoute = new Zend_Controller_Router_Route('api');
        $restRoute = new Kwf_Rest_Route(
            $this->getFrontController(),
            array(
                'module' => 'api'
            ),
            array('api')
        );
        $chainedRoute = new Kwf_Controller_Router_Route_Chain();
        $chainedRoute->chain($apiRoute)
                    ->chain($restRoute);
        $this->addRoute('api', $chainedRoute);

        //Komponenten routes
        if ($prefix) {
            $prefix = '/'.$prefix;
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
