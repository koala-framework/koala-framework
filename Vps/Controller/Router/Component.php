<?php
class Vps_Controller_Router_Component extends Vps_Controller_Router
{
    public function __construct(array $params = array())
    {
        parent::__construct($params);
        $this->AddRoute('admin', new Zend_Controller_Router_Route(
                    '/admin/:module/:controller/:action',
                    array('module'=>'vps_controller_action_component',
                          'controller' => 'index',
                          'action' => 'index')));
        $this->AddRoute('component', new Zend_Controller_Router_Route(
                    '/admin/component/:controller/:action',
                    array('module'=>'vps_controller_action_component',
                          'action' => 'index')));
        $this->AddRoute('components', new Zend_Controller_Router_Route(
                    '/admin/components/:action',
                    array('module' => 'vps_controller_action_component',
                          'controller' => 'components',
                          'action' => 'index')));
        $this->AddRoute('componentshow', new Zend_Controller_Router_Route(
                    '/admin/component/show/:class/:componentId',
                    array('componentId'=>null,
                          'module' => 'vps_controller_action_component',
                          'controller' => 'components',
                          'action' => 'show')));
        $this->AddRoute('componentjsonshow', new Zend_Controller_Router_Route(
                    'admin/component/json-show/:class/:componentId',
                    array('componentId'=>null,
                          'module' => 'vps_controller_action_component',
                          'controller' => 'components',
                          'action' => 'jsonshow')));
        $this->AddRoute('componentedit', new Zend_Controller_Router_Route(
                    '/admin/component/edit/:class/:componentController/:action',
                    array('module' => 'component',
                          'controller' => 'component',
                          'action' => 'index')));
    }
}
