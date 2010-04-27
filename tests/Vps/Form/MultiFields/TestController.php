<?php
// zum manuell testen
// /vps/test/vps_form_multi-fields_test
class Vps_Form_MultiFields_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    public function preDispatch()
    {
        $this->_model = Vps_Model_Abstract::getInstance('Vps_Form_MultiFields_TestModel1');
        parent::preDispatch();
    }

    protected function _initFields()
    {
        $fs = $this->_form;

        $mf = $fs->add(new Vps_Form_Field_MultiFields('Model2'));
        $mf->fields->add(new Vps_Form_Field_TextField('seltestfield', 'Sel Test Label'));
    }

    public function indexAction()
    {
        $config = $this->_form->getProperties();
        if (!$config) { $config = array(); }
        $config['baseParams']['id'] = 1;
        $config = array_merge(
            $config,
            array(
                'controllerUrl' => $this->getRequest()->getPathInfo(),
                'assetsType' => 'Vps_Form_MultiFields:Test',
            )
        );
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}

