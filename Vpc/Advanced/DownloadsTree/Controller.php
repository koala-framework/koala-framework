<?php
class Vpc_Advanced_DownloadsTree_Controller extends Vps_Controller_Action
{
    public function indexAction()
    {
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $config = $config['customerarea'];
        $config['baseParams']['componentId'] = $this->_getParam('componentId');
        $this->view->assign($config);
        $this->view->baseParams = array('componentId' => $this->_getParam('componentId'));
    }

    public function jsonIndexAction()
    {
        $this->indexAction();
    }
}
