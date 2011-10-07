<?php
abstract class Vps_Controller_Action_Auto_Vpc_Form extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save', 'saveBack');
    protected $_permissions = array('save', 'add');
    protected $_formName;

    public function preDispatch()
    {
        if (!isset($this->_form)) {
            if (isset($this->_formName)) {
                $this->_form = new $this->_formName(null, $this->_getParam('class'));
            } else {
                $this->_form = Vpc_Abstract_Form::createComponentForm($this->_getParam('class'), 'component');
            }
        }
        
        $this->_form->setBodyStyle('padding: 10px');
        $this->_form->setId($this->_getParam('componentId'));
        parent::preDispatch();
    }
    public function indexAction()
    {
        parent::indexAction();
        $this->view->assign(Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig());
        $this->view->baseParams = array(
            'id' => $this->_getParam('componentId'),
            'componentId' => $this->_getParam('componentId')
        );
        if ($this->getRequest()->module == 'component_test' && isset($this->view->controllerUrl)) {
            $this->view->controllerUrl = str_replace('/admin/component/edit/',
                        '/vps/componentedittest/'.Vps_Component_Data_Root::getComponentClass().'/',
                        $this->view->controllerUrl);
        }
    }
}
