<?php
class Vps_Form_CheckboxFieldsetInFieldset_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_CheckboxFieldsetInFieldset_TestModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $fs1 = $this->_form->add(new Vps_Form_Container_FieldSet("Foo"))
            ->setCheckboxToggle(true)
            ->setCheckboxName('fs1');
        $fs2 = $fs1->add(new Vps_Form_Container_FieldSet("Bar"))
            ->setCheckboxToggle(true)
            ->setCheckboxName('fs2');
        $fs2->add(new Vps_Form_Field_TextField("text", "Text"))
            ->setAllowBlank(false);

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
                'assetsType' => 'Vps_Form_CheckboxFieldsetInFieldset:Test',
            )
        );
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}

