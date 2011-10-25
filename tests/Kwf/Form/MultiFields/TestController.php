<?php
// zum manuell testen
// /kwf/test/kwf_form_multi-fields_test
class Kwf_Form_MultiFields_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    public function preDispatch()
    {
        $this->_model = Kwf_Model_Abstract::getInstance('Kwf_Form_MultiFields_TestModel1');
        parent::preDispatch();
    }

    protected function _initFields()
    {
        $fs = $this->_form;

        $mf = $fs->add(new Kwf_Form_Field_MultiFields('Model2'));
        $mf->fields->add(new Kwf_Form_Field_TextField('seltestfield', 'Sel Test Label'));
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
                'assetsType' => 'Kwf_Form_MultiFields:Test',
            )
        );
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

