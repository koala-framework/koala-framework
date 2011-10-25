<?php
// zum manuell testen
// /kwf/test/kwf_form_multi-checkbox_test
class Kwf_Form_MultiCheckbox_TestController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    public function preDispatch()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Form_MultiCheckbox_DataModel');
        $model->setData(
            array(array('id' => 1))
        );
        $this->_model = $model;
        parent::preDispatch();
    }

    protected function _initFields()
    {
        $fs = $this->_form;

        $sel = new Kwf_Model_Select();
        $sel->where(new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equal('id', 1),
            new Kwf_Model_Select_Expr_Equal('id', 2)
        )));
        $fs->add(new Kwf_Form_Field_MultiCheckbox('Relation', 'Value', 'Relations only'))
            ->setValuesSelect($sel);

        $sel = new Kwf_Model_Select();
        $sel->whereEquals('id', 3);
        $fs->add(new Kwf_Form_Field_MultiCheckbox(
            Kwf_Model_Abstract::getInstance('Kwf_Form_MultiCheckbox_RelationModel'),
            'Value',
            'Model and relation'
        ))->setValuesSelect($sel);

        $fs->add(new Kwf_Form_Field_MultiCheckbox(
            Kwf_Model_Abstract::getInstance('Kwf_Form_MultiCheckbox_RelationModelNoRel'),
            'Value',
            'Model and relation (no dataToRelation)'
        ));

        $fs->add(new Kwf_Form_Field_MultiCheckbox('Relation', 'Value', 'setShowCheckAllLinks(false)'))
            ->setShowCheckAllLinks(false);
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
                'assetsType' => 'Kwf_Form_MultiCheckbox:Test',
            )
        );
        $this->view->ext('Kwf.Auto.FormPanel', $config, 'Kwf.Test.Viewport');
    }
}

