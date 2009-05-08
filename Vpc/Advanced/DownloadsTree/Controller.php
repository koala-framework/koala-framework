<?php
class Vpc_Advanced_DownloadsTree_Controller extends Vps_Controller_Action
{
    public function indexAction()
    {
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $config['baseParams']['componentId'] = $this->_getParam('componentId');
        $config['projectsClass'] = Vpc_Admin::getComponentClass($this->_getParam('class'), 'ProjectsController');
        $config['projectClass'] = Vpc_Admin::getComponentClass($this->_getParam('class'), 'ProjectController');
        $config['downloadsClass'] = Vpc_Admin::getComponentClass($this->_getParam('class'), 'DownloadsController');
        $config['projectsClass'] = str_replace('Controller', '', $config['projectsClass']);
        $config['projectClass'] = str_replace('Controller', '', $config['projectClass']);
        $config['downloadsClass'] = str_replace('Controller', '', $config['downloadsClass']);
        $this->view->vpc($config);
    }

    public function jsonIndexAction()
    {
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $this->view->vpc($config);
    }
}
