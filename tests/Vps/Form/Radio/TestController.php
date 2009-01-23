<?php
// zum manuell testen; gibt keinen test der den controller verwendet
// /vps/test/vps_form_radio-test
class Vps_Form_Radio_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_Radio_TestModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField("text", "Text"));
        $this->_form->add(new Vps_Form_Field_Radio("foo", "Foo"))
            ->setValues(array(
                'foo' => 'Foo',
                'bar' => 'Bar'
            ));
    }

    public function indexAction()
    {
        $config = $this->_form->getProperties();
        if (!$config) { $config = array(); }
        $config['baseParams']['id'] = 1;
        $config = array_merge(
            $config,
            array(
                'controllerUrl' => $this->getRequest()->getPathInfo()
            )
        );
        $this->view->ext('Vps.Auto.FormPanel', $config);
    }
}

