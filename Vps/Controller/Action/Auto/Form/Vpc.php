<?php
abstract class Vps_Controller_Action_Auto_Form_Vpc extends Vps_Controller_Action_Auto_Form {
    public function indexAction()
    {
       $this->view->ext('Vps.Auto.Form');
    }
       
    public function jsonIndexAction()
    {
       $this->indexAction();
    }
    public function jsonLoadAction()
    {
        $defaultSettings = $this->component->getDefaultSettings(); // Komponente ist unter $this->component zu finden
        $this->_table->createDefaultRow($this->_getParam('id'), $defaultSettings);
        parent::jsonLoadAction();
    }
}
