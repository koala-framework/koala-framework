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
        $this->AddRoute('trl', new Zend_Controller_Router_Route(
                    '/vps/trl/:controller/:action',
                    array('module'     => 'vps_controller_action_trl',
                          'controller' => 'index',
                          'action'     => 'index')));
        $this->AddRoute('trl', new Zend_Controller_Router_Route(
                    '/vps/debug/:controller/:action',
                    array('module'     => 'vps_controller_action_debug',
                          'controller' => 'index',
                          'action'     => 'index')));
    }
}
