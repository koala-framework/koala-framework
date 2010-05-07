<?php
// zum manuell testen
// /vps/test/vps_form_multi-checkbox_test
class Vps_Form_MultiCheckbox_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_MultiCheckbox_DataModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $fs = $this->_form;

        $sel = new Vps_Model_Select();
        $sel->where(new Vps_Model_Select_Expr_Or(array(
            new Vps_Model_Select_Expr_Equals('id', 1),
            new Vps_Model_Select_Expr_Equals('id', 2)
        )));
        $fs->add(new Vps_Form_Field_MultiCheckbox('Relation', 'Value', 'Relations only'))
            ->setValuesSelect($sel);

        $sel = new Vps_Model_Select();
        $sel->whereEquals('id', 3);
        $fs->add(new Vps_Form_Field_MultiCheckbox(
            Vps_Model_Abstract::getInstance('Vps_Form_MultiCheckbox_RelationModel'),
            'Value',
            'Model and relation'
        ))->setValuesSelect($sel);

        $fs->add(new Vps_Form_Field_MultiCheckbox(
            Vps_Model_Abstract::getInstance('Vps_Form_MultiCheckbox_RelationModelNoRel'),
            'Value',
            'Model and relation (no dataToRelation)'
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
                'controllerUrl' => $this->getRequest()->getPathInfo(),
                'assetsType' => 'Vps_Form_MultiCheckbox:Test',
            )
        );
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}

