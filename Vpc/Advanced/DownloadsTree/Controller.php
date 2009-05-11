<?php
class Vpc_Advanced_DownloadsTree_Controller extends Vps_Controller_Action
{
    public function indexAction()
    {
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $config['baseParams']['componentId'] = $this->_getParam('componentId');
        $config['projectsUrl'] = Vpc_Admin::getInstance(get_class($this))->getControllerUrl('Projects');
        $config['projectUrl'] = Vpc_Admin::getInstance(get_class($this))->getControllerUrl('Project');
        $config['downloadsUrl'] = Vpc_Admin::getInstance(get_class($this))->getControllerUrl('Downloads');
        $this->view->vpc($config);
    }

    public function jsonIndexAction()
    {
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $this->view->vpc($config);
    }
}
