<?php
class Vpc_Formular_Textbox_IndexController extends Vps_Controller_Action_Auto_Form
{
    protected $_fields = array(
            array('type'       => 'TextField',
                  'fieldLabel' => 'Name',
                  'name'       => 'name'),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Breite (in Pixel)',
                  'name'       => 'width',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Maximale Textlänge',
                  'name'       => 'maxlength',
                  'width'      => 50),
        );

    protected $_buttons = array('save'   => true);
    protected $_tableName = 'Vpc_Formular_Textbox_IndexModel';

    public function indexAction()
    {
        //$defaultSettings = $this->component->getDefaultSettings(); // Komponente ist unter $this->component zu finden
        $defaultSettings = array();
        $this->_table->createDefaultRow($this->_getParam('id'), $defaultSettings);
        $this->view->ext('Vps.Auto.Form');
    }
       
    public function jsonIndexAction()
    {
        $this->indexAction();
    }
       
}
