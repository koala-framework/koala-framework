<?php
class Vpc_Controller extends Vps_Controller_Action {
    
    protected function _createComponent()
    {
        $id = $this->getRequest()->getParam('id');
        $component = Vpc_Abstract::getInstance(Zend_Registry::get('dao'), $id)->findComponent($id);
        if (!$component) {
            throw new Vpc_Exception('Component not found.');
        }
        return $component;
    }
    
    protected function _render($paths = array(), $class = '', $config = array())
    {
        if ($class == '') {
            $class = str_replace('_', '.', str_replace('_Controller', '_Index', get_class($this)));
            $paths[] = '/' . str_replace('.', '/', $class) . '.js';
        }

        $config['id'] = $this->getRequest()->getParam('id');
        $view = new Vps_View_Smarty_Ext($paths, $class, $config);
        $this->getResponse()->setBody($view->render(''));
    }
    
}