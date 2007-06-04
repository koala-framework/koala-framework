<?php
class Vpc_Simple_Textbox_Controller extends Vpc_Controller
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
        $component = Vpc_Abstract::getInstance(Zend_Registry::get('dao'), $id)->findComponent($id);
        if (!$component) {
            throw new Vpc_Exception('Component not found.');
        }
        return $component;
    }

    protected function _render($config = array())
    {
        $controllerpath = str_replace('_', '/', str_replace('Vpc_', '', get_class($this))) . '.js';
        $controllername = str_replace('_Controller', '', get_class($this));
        $controllername = substr($controllername, strrpos($controllername, '_') + 1);
        $config['id'] = $this->getRequest()->getParam('id');
        
        $view = new Vps_View_Smarty_Ext(array('/Vpc/' . $controllerpath), $controllername, $config);
        $this->getResponse()->setBody($view->render(''));
    }
    
}
