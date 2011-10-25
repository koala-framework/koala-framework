<?php
class Kwf_Form_GoogleMap_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Form_GoogleMap_TestModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_GoogleMapsField('mapSelected', 'mapSelected'));
        $this->_form->add(new Kwf_Form_Field_GoogleMapsField('mapEmpty', 'mapEmpty'));
    }

    protected function _getResourceName()
    {
        return 'kwf_test';
    }
    public function indexAction()
    {
        $config = array();
        $config['baseParams']['id'] = 1;
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsType'] = 'Kwf_Form_GoogleMap:Test';
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

