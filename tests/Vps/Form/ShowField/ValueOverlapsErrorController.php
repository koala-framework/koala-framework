<?php
class Vps_Form_ShowField_ValueOverlapsErrorController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Form_ShowField_ValueOverlapsModel';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_ShowField('firstname', 'Vorname'));
        $this->_form->add(new Vps_Form_Field_ShowField('lastname', 'Nachname'));

        $cards = $this->_form->add(new Vps_Form_Container_Cards('type', trlVps('Type')))
            ->setValues(array("storage"));

        $form = new Vps_Kitepower_Form_Booking_ServiceStorage('storage');
        $card = $cards->add();
        $title = "Storage";
        $title = str_replace('.', ' ', $title);
        $card->setTitle($title);
        $card->setName("storage");
        if ($form) $card->add($form)->setServiceType('storage');
    }

    protected function _getResourceName()
    {
        return 'vps_test';
    }

    public function indexAction()
    {
        $config = array();
        $config['baseParams']['id'] = 1;
        $config['controllerUrl'] = $this->getRequest()->getPathInfo();
       // $this->view->ext('Vps.Auto.FormPanel', $config);
        $this->view->ext('Vps.Test.OverlapsError', array(
            'assetsType' => 'AdminTest',
            'controllerUrl' => '/vps/test/vps_form_show-field_value-overlaps-error-form',
        ), 'Vps.Test.Viewport');
    }
}

