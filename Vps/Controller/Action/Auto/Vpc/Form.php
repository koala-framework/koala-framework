<?php
abstract class Vps_Controller_Action_Auto_Vpc_Form extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save' => true);
    protected $_formName = 'Vps_Auto_Vpc_Form';
    
    public function preDispatch()
    {
        if (!isset($this->_form)) {
            $this->_form = new $this->_formName($this->class,
                        array('page_id'=> $this->pageId,
                              'component_key'=> $this->componentKey));
        }
        $this->_form->setBodyStyle('padding: 10px');
        parent::preDispatch();
    }

    public function indexAction()
    {
        $config = Vpc_Admin::getConfig($this->class, $this->pageId, $this->componentKey);
        $this->view->vpc($config);
    }

    public function jsonIndexAction()
    {
       $this->indexAction();
    }

}
