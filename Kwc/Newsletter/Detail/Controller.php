<?php
class Kwc_Newsletter_Detail_Controller extends Kwf_Controller_Action_Auto_Kwc_Form
{
    protected $_formName = 'Kwc_Newsletter_Detail_Form';

    public function indexAction()
    {
        $config = Kwc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $this->view->assign($config['form']);
        $this->view->baseParams = array('componentId' => $this->_getParam('componentId'));
    }
}