<?php
class Vpc_Formular_Index extends Vpc_Paragraphs_Abstract 
{
    //protected $_defaultSettings = array('names' => array());
    private $_errors = array();
    private $_fields = array();
    private $_components = array();
    public function getTemplateVars($mode)
    {
        
        //$this->_checkForm();
        
        if ($_POST != array()) {
            if ($this->_validateFields()) {
                d("Formular wurde abgeschickt");
                
            } else {
                p("Fehler in der Eingabe");
            }
        } else {
           
             $this->_getFormFields();
        }        
       
        $vars = parent::getTemplateVars($mode);
        $vars['action'] = $_SERVER['REQUEST_URI'];
        $vars['errors'] = $this->_errors;
        $vars['names'] = $this->_fields;
        $vars['id'] = $this->getComponentId(); 
        $vars['upload'] = $this->_checkUpload();
        $vars['submit'] = $this->_checkIfSubmit();  
        $vars['template'] = 'Formular.html';
        return $vars;
    }
    
    /**
     * Holt die Formularfelder und setzt den Namen für das jeweilige Feld auf 
     * Basis der Formulartabelle -> es wird ein eintrag in die Datenbank vorgenommen
     */
    private function _getFormFields () {
        $fields = array();
        $this->_components = $this->getChildComponents();
        $names = array();
        
        foreach ($this->_components AS $componentKey => $component){            
              $rows = $this->_getTable()->fetchAll(array('component_id = ?'  => $component->getComponentId()));
            foreach ($rows AS $row) {
               //--------
                $filter = new Zend_Filter_Alpha();
                $newName = $filter->filter($row->name);
                $tempName = $newName;
                $cnt = 1;
                while (in_array($newName, $names)){
                    $newName = $tempName.$cnt;
                    $cnt++;
                }
                
                $names[] = $newName;
                if ($component instanceof Vpc_Formular_Field_Interface ) {	               
	                $component->setName($newName);
	                $component->setErrorField($row->name);
	                $this->_components[$componentKey] = $component;
                }           
	            $fields[] = array ('name' => $row->name, 'id' => $row->component_id, 'mandatory' => $row->mandatory, 'noCols' => $row->no_cols, 'isValid' => 1);    
            }
            
        }
        $this->_fields = $fields;
    }
    
    
    /*private function _checkForm()
    {
        
        if (!$this->_checkIfSubmit()) {
             
            $submit = $this->createComponent('Vpc_Formular_Submit_Index', $this->getComponentId(), 2);
            $submit->setSetting('name', 'Absenden');
            $submit->setSetting('value', 'Absenden');
            $this->addChildComponent($submit);
        }
    }*/
    
    private function _checkIfSubmit()
    {
        $this->_components = $this->getChildComponents();           
        
        foreach($this->_components as $value => $component) {
            if ($component instanceof Vpc_Formular_Submit_Index) {
                return true;
            }
        }
        return false;
    }
    
    private function _checkUpload()
    {
        $this->_components = $this->getChildComponents();           
        
        foreach($this->_components as $value => $component) {
            if ($component instanceof Vpc_Formular_FileUpload_Index) {
                return true;
            }
        }
        return false;
    }
    
    private function _validateFields()
    {
        $this->_getFormFields();
        $return = true;
        
       // $components = $this->getChildComponents();
        foreach($this->_components as $value => $component) {
            if ($component instanceof Vpc_Formular_Field_Interface) {
                $row = $this->_getTable()->fetchAll(array('component_id = ?'  => $component->getComponentId()))->current();               
                //if ($component instanceof Vpc_Formular_Field_Decide_Abstract)
                $component->processInput();
                if ($component->validateField($row->mandatory) !== true) {
                    $return = false;
                  //  $fieldname = $rows = $this->_getTable()->fetchAll(array('component_id = ?'  => $component->getComponentId()))->current();
                    $this->_errors[] = $component->validateField($row->mandatory);
                    $this->_notValid($component->getComponentId());
                }
            }            
        }
        return $return;
    }
    
    private function _notValid ($id){
        foreach ($this->_fields AS $fieldkey => $field){
            
            if ($field['id'] == $id){
                $field['isValid'] = 0;
            }           
            $this->_fields[$fieldkey] = $field;
          
        }
    }    
}


