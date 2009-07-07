<?php
class Vpc_Advanced_DownloadsTree_Controller extends Vps_Controller_Action
{
    public function indexAction()
    {
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $config['baseParams']['componentId'] = $this->_getParam('componentId');
        $config['projectsUrl'] = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Projects');
        $config['projectUrl'] = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Project');
        $config['downloadsUrl'] = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Downloads');
        $this->view->assign($config);
        $this->view->baseParams = array('componentId' => $this->_getParam('componentId'));
    }

    public function jsonIndexAction()
    {
        $this->indexAction();
    }
}
