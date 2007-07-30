<?php
abstract class Vps_Controller_Action_Auto_Form_Vpc extends Vps_Controller_Action_Auto_Form {
    public function indexAction()
    {
       $this->view->ext('Vps.Auto.Form');
    }

    /*public function jsonIndexAction()
    {
    	 $info = $this->_table->info();
		$check = $info['cols'];
		d ($check);
       $this->indexAction();
    }
    public function jsonLoadAction()
    {
        $defaultSettings = $this->component->getDefaultSettings(); // Komponente ist unter $this->component zu finden
        $info = $this->_table->info();
		$check = $info['cols'];
		$newarray = array_intersect_key($defaultSettings, $check);
        $this->_table->createDefaultRow($this->_getParam('id'), $newarray);
        parent::jsonLoadAction();
    }*/

    public function jsonIndexAction()
    {
       $info = $this->_table->info();
	   $check = $info['cols'];
       $this->indexAction();
    }

    public function jsonLoadAction()
    {
        $defaultSettings = $this->component->getDefaultSettings(); // Komponente ist unter $this->component zu finden
        $info = $this->_table->info();
		$check = $info['cols'];
		$keys = array_keys($defaultSettings);
		$newArray = array();
		foreach ($check AS $checkKey => $checkValue){
			if (in_array($checkValue, $keys)){
				$newArray[$checkValue] = $defaultSettings[$checkValue];
			}
		}
        $this->_table->createDefaultRow($this->_getParam('id'), $newArray);
        parent::jsonLoadAction();
    }
}
