<?php
class Vpc_Newsletter_Detail_Controller extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function _initFields()
    {
        parent::_initFields();
        $this->_form->setId($this->_getNewsletterId());
        $this->_form->setModel(new Vpc_Newsletter_Model());
    }

    public function indexAction()
    {
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $this->view->assign($config['form']);
        $this->view->baseParams = array('componentId' => $this->_getParam('componentId'));
    }

    private function _getNewsletterId()
    {
        return (int)substr(strrchr($this->_getParam('componentId'), '_'), 1);
    }
}