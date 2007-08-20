<?php
class Vpc_Formular_Index extends Vpc_Paragraphs_Abstract
{
    protected $_tablename = 'Vpc_Formular_IndexModel';
    const NAME = 'Formular';

    private $_errors = array();
    private $_components = array();
    
    public function getTemplateVars()
    {
        $sent = 1;
        if ($_POST != array()) {
            if ($this->_validateFields()) {
                $sent = 3;
            } else {
                $sent = 2;
            }
        }
        $vars = parent::getTemplateVars();
        $vars['sent'] = $sent;
        $vars['action'] = $_SERVER['REQUEST_URI'];
        $vars['errors'] = $this->_errors;
        $vars['upload'] = $this->_hasUpload();
        $vars['template'] = 'Formular.html';
        return $vars;
    }

    /**
     * Holt die Formularfelder und setzt den Namen für das jeweilige Feld auf
     * Basis der Formulartabelle -> es wird ein eintrag in die Datenbank vorgenommen
     */
    public function getChildComponents()
    {
        if (!$this->_components) {
            foreach ($this->getPreparedData() as $row){
                $row = (object)$row;
                $component = $this->createComponent($row->component_class, $row->id);
                $component->store('description', $row->description);
                $component->store('isMandatory', $component instanceof Vpc_Formular_Captcha_Index || $row->mandatory == 1);
                $component->store('noCols', $row->no_cols == 1);
                $component->store('isValid', true);
                if ($component instanceof Vpc_Formular_Field_Interface ) {
                    $component->setSetting('name', $component->getId());
                }
                $this->_components[] = $component;
            }
        }
        
        return $this->_components;
    }
    
    private function getPreparedData()
    {
        if (!$this->_data) {
            $data = $this->_getData()->toArray();
            $hasSubmit = false;
            foreach ($data as $d) {
                if ($d['component_class'] == 'Vpc_Formular_Submit_Index') {
                    $hasSubmit = true;
                }
            }
            if (!$hasSubmit) {
                $d['id'] = 0;
                $d['component_class'] = 'Vpc_Formular_Submit_Index';
                $d['name'] = 'submit';
                $d['description'] = '';
                $d['no_cols'] = '1';
                $d['mandatory'] = '0';
                $data[] = $d;
            }
            $this->_data = $data;
        }
        return $this->_data;
    }
    
    
    
    private function _hasUpload()
    {
        foreach($this->getChildComponents() as $value => $component) {
            if ($component instanceof Vpc_Formular_FileUpload_Index) {
                return true;
            }
        }
        return false;
    }

    private function _validateFields()
    {
        $return = true;
        foreach($this->getChildComponents() as $value => $component) {
            if ($component instanceof Vpc_Formular_Field_Interface) {
                $component->processInput();
                $message = $component->validateField($component->getStore('isMandatory'));
                if ($message != '') {
                    $return = false;
                    $this->_errors[] = $message;
                    $component->store('isValid', false);
                }
            }
        }
        return $return;
    }

}


