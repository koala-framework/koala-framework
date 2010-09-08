<?php
class Vps_Form_Cards_NotAllowBlank_TestController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_Cards_NotAllowBlank_Model';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_temp = "";

    protected function _initFields()
    {
        $cards = $this->_form->add(new Vps_Form_Container_Cards('type', trlVps('Type')));
        $cards->setCombobox(new Vps_Form_Field_Radio('type', trlVps('Type')));

        $card = $cards->add();
        $card->setTitle('foocard');
        $card->setName("foo");

        $card->add(new Vps_Form_Field_TextField('comment', 'Comment'));


        $card = $cards->add();
        $card->setTitle('barcard');
        $card->setName("bar");
        $fieldset = $card->add(new Vps_Form_Container_FieldSet(trl('Fieldset legend')));

        $mf = $fieldset->add(new Vps_Form_Field_MultiFields('ToRelation'));
        $mf->setMinEntries(0);

        $mf->fields->add(new Vps_Form_Field_Select('data_id', 'Select Value'))
            ->setValues(array(
                array('id' => 1, 'value' => 'v1'),
                array('id' => 2, 'value' => 'v2'),
                array('id' => 3, 'value' => 'v3')
            ))
            ->setEditable(true)
            ->setForceSelection(true)
            ->setAllowBlank(false);
    }

    public function indexAction()
    {
        $config = array();
        $config['baseParams']['id'] = $this->_getParam('id');
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $config['assetsType'] = 'Vps_Form_Cards_NotAllowBlank:Test';
        $this->view->ext('Vps.Auto.FormPanel', $config, 'Vps.Test.Viewport');
    }
}

