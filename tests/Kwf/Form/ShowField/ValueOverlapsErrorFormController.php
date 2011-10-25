<?php
class Kwf_Form_ShowField_ValueOverlapsErrorFormController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Form_ShowField_ValueOverlapsModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_ShowField('firstname', 'Vorname'));
        $this->_form->add(new Kwf_Form_Field_ShowField('lastname', 'Nachname'));

        $cards = $this->_form->add(new Kwf_Form_Container_Cards('type', trlKwf('Type')))
            ->setValues(array("storage"));

        $form = new Kwf_Form_ShowField_Storage('storage');
        $card = $cards->add();
        $title = "Storage";
        $title = str_replace('.', ' ', $title);
        $card->setTitle($title);
        $card->setName("storage");
        if ($form) $card->add($form)->setServiceType('storage');
    }

    public function indexAction()
    {
        $config = array();
        $config['baseParams']['id'] = 1;
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
        $this->view->ext('Kwf.Test.OverlapsError', array(
            'assetsType' => 'Kwf_Form_ShowField:Test',
            'controllerUrl' => '/kwf/test/kwf_form_show-field_value-overlaps-error-form',
        ), 'Kwf.Test.Viewport');
    }
}


