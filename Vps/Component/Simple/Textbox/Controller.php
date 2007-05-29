<?php
class Vps_Component_Simple_Textbox_Controller extends Vps_Controller_Action
{
    public function indexAction()
    {
        $config['content'] = $this->_createComponent()->retrieveContent();
        $this->_render($config);
    }
    
    public function ajaxSaveDataAction()
    {
        $content = $this->getRequest()->getParam('content');
        $data['success'] = $this->_createComponent()->saveContent($content);
        $this->getResponse()->setBody(Zend_Json::encode($data));
    }
    
    protected function _createComponent()
    {
        $id = $this->getRequest()->getParam('id');
        $component = Vps_Component_Abstract::getInstance(Zend_Registry::get('dao'), $id)->findComponent($id);
        if (!$component) {
            throw new Vps_Component_Exception('Component not found.');
        }
        return $component;
    }

    protected function _render($config = array())
    {
        $controllerpath = str_replace('_', '/', str_replace('Vps_Component_', '', get_class($this))) . '.js';
        $controllername = str_replace('_Controller', '', get_class($this));
        $controllername = substr($controllername, strrpos($controllername, '_') + 1);
        $view = new Vps_View_Smarty('../library/Vps/Component');
        $view->assign('controllerFile',  $controllerpath);
        $view->assign('controller', $controllername);
        $view->assign('config', Zend_Json::encode($config));
        $view->assign('id', $this->getRequest()->getParam('id'));
        $body = $view->render('Controller.html');
        $this->getResponse()->setBody($body);
    }
    
}
