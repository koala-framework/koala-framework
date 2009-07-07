<?php
class Vpc_Newsletter_Detail_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_formName = 'Vpc_Newsletter_Detail_Form';

    public function indexAction()
    {
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $this->view->assign($config['form']);
        $this->view->baseParams = array('componentId' => $this->_getParam('componentId'));
    }
}