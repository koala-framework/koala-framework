<?php
class Vpc_Simple_Textbox_Controller extends Vpc_Controller
{
    public function indexAction()
    {
        $config['content'] = $this->_createComponent()->getContent();
        $this->_render(array(), '', $config);
    }
    
    public function ajaxSaveDataAction()
    {
        $content = $this->getRequest()->getParam('content');
        $this->view->success = $this->_createComponent()->saveContent($content);
    }
    
}
