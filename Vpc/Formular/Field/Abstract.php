<?php

abstract class Vpc_Formular_Field_Abstract extends Vpc_Abstract implements Vpc_Formular_Field_Interface
{
    protected $_errorField = '';
    
    public function processInput()
    {
        
    }
    public function validateField($mandatory)
    {
        
    }
    
    public function getName()
    {
        return $this->getSetting('name');
    }
    
    public function setName($name) 
    {
        $this->setSetting('name', $name);
    }
    
    public function setErrorField($fieldname)
    {
        $this->_errorField = $fieldname;
    }
    
}