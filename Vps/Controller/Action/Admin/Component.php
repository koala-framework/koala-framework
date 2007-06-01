<?php
class Vps_Controller_Action_Admin_Component extends Vps_Controller_Action
{
    protected $_auth = true;
    
    public function __call($action, $args)
    {
        $id = $this->getRequest()->getParam('id');
        $component = Vpc_Abstract::getInstance(Zend_Registry::get('dao'), $id);
        $component = $component->findComponent($id);
        $controller = substr(get_class($component), 0, strrpos(get_class($component), '_') + 1) . 'Controller';
        $action = str_replace('Action', '', $action);
        if ($action == 'action') { $action = 'index'; }
        try {
            Zend_Loader::LoadClass($controller);
            $this->_forward($action, $controller, 'component', $this->getRequest()->getParams());
        } catch (Zend_Exception $e) {
            $this->getResponse()->setBody('Editing does not exist for this component. Try Frontend-Editing instead.');
        }
    }

}