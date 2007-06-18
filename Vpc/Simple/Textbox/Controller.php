<?php
class Vpc_Simple_Textbox_Controller extends Vps_Controller_Action
{
    public function indexAction()
    {
        $config['content'] = $this->component->getContent();
        $this->view->vpc($config);
    }
    
    public function ajaxSaveDataAction()
    {
        $content = $this->getRequest()->getParam('content');
        $this->view->success = $this->component->saveContent($content);
    }
    
}
