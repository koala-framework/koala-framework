<?php
class Vpc_Abstract_List_ControllerWithEditButton extends Vps_Controller_Action_Auto_Vpc_Form
{
    public function indexAction()
    {
        //nicht: parent::indexAction();
        $this->view->xtype = 'vps.component';
        $this->view->mainComponentClass = $this->_getParam('class');
        $this->view->baseParams = array('id' => $this->_getParam('componentId'));

        $this->view->componentConfigs = array();
        $this->view->mainEditComponents = array();
        $config = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        if (!$config) {
            throw new Vps_Exception("Not ExtConfig avaliable for this component");
        }
        foreach ($config as $k=>$c) {
            $this->view->componentConfigs[$this->_getParam('class').'-'.$k] = $c;
            $this->view->mainEditComponents[] = array(
                'componentClass' => $this->_getParam('class'),
                'type' => $k
            );
        }
        $this->view->mainType = $this->view->mainEditComponents[0]['type'];
    }
}
