<?php
class Vps_Controller_Router extends Zend_Controller_Router_Rewrite
{
    public function __construct(array $params = array())
    {
        parent::__construct($params);
        $this->AddRoute('vps_welcome', new Zend_Controller_Router_Route(
                    '/vps/welcome/:controller/:action',
                    array('module'     => 'vps_controller_action_welcome',
                          'controller' =>'index',
                          'action'     =>'index')));
        $this->AddRoute('vps_user', new Zend_Controller_Router_Route(
                    '/vps/user/:controller/:action',
                    array('module'     => 'vps_controller_action_user',
                          'action'     =>'index')));
        $this->AddRoute('vps_error', new Zend_Controller_Router_Route(
                    '/vps/error/:controller/:action',
                    array('module'     => 'vps_controller_action_error')));
        $this->AddRoute('vps_start', new Zend_Controller_Router_Route(
                    '/vps/start',
                    array('module'      => 'vps_controller_action_welcome',
                          'controller'  => 'start',
                          'action'      => 'index')));
        $this->AddRoute('vps_pool', new Zend_Controller_Router_Route(
                    '/vps/pool/:controller/:action',
                    array('module'     => 'vps_controller_action_pool',
                          'controller' => 'pools',
                          'action'     => 'index')));
        $this->AddRoute('vps_todo', new Zend_Controller_Router_Route(
                    '/vps/todo/:controller/:action',
                    array('module'     => 'vps_controller_action_todo',
                          'controller' => 'todos',
                          'action'     => 'index')));
        $this->AddRoute('trl', new Zend_Controller_Router_Route(
                    '/vps/trl/:controller/:action',
                    array('module'     => 'vps_controller_action_trl',
                          'controller' => 'index',
                          'action'     => 'index')));
        $this->AddRoute('debug', new Zend_Controller_Router_Route(
                    '/vps/debug/:controller/:action',
                    array('module'     => 'vps_controller_action_debug',
                          'controller' => 'index',
                          'action'     => 'index')));
        $this->AddRoute('media', new Zend_Controller_Router_Route(
                    '/vps/media/:controller/:action',
                    array('module'     => 'vps_controller_action_media',
                          'controller' => 'index',
                          'action'     => 'index')));
        $this->AddRoute('vps_spam', new Zend_Controller_Router_Route(
                    '/vps/spam/:controller/:action',
                    array('module'     => 'vps_controller_action_spam',
                          'action'     =>'index')));

        //für selenium-tests von sachen die im vps liegen
        $this->AddRoute('vps_test', new Zend_Controller_Router_Route(
                    '/vps/test/:controller/:action',
                    array('module'     => 'vps_test',
                          'action'     =>'index')));
        $this->AddRoute('vps_vpctest', new Zend_Controller_Router_Route_Regex(
                    'vps/vpctest/([^/]+)/(.*)',
                    array('module'     => 'vps_test',
                          'controller' => 'vpc_test',
                          'action'     => 'index',
                          'url'        => ''),
                    array('root'=>1, 'url'=>2)));
        $this->AddRoute('vps_test_componentedit', new Zend_Controller_Router_Route(
                    '/vps/componentedittest/:root/:class/:action',
                    array('module' => 'component_test',
                          'controller' => 'component_test',
                          'action' => 'index')));

        //für selenium-tests von sachen die im web liegen
        $this->AddRoute('vps_test', new Zend_Controller_Router_Route(
                    '/vps/webtest/:controller/:action',
                    array('module'     => 'web_test',
                          'action'     =>'index')));
    }
}
