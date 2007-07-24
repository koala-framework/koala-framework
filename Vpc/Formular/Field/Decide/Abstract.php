<?php
abstract class Vpc_Formular_Field_Decide_Abstract extends Vpc_Formular_Field_Abstract
{
    protected $_options = array();
    
    public function processInput()
    {        
        $type = "selected";
        if ($this instanceof Vpc_Formular_Option_Index) {
            $type = "checked";
        }
        
        if (isset($_POST[$this->getName()])) {
            $this->getOptions();
            foreach($this->_options AS $key => $option) {
                if ($option['value'] == $_POST[$this->getName()]) {
                    $option[$type] = '1';
                } else {
                    $option[$type] = '0';
                }
                $this->_options[$key] = $option;
            }
        }
        return true;
    }
    
    public function validateField($mandatory)
    {
        if(!isset($_POST[$this->getSetting('name')]) && $mandatory == 1) return 'Feld '.$this->_errorField.' ist ein Pflichtfeld, bitte ausfüllen';
        return true;
    }
    
    
}