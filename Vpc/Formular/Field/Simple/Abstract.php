<?php
abstract class Vpc_Formular_Field_Simple_Abstract extends Vpc_Formular_Field_Abstract
{

    public function processInput()
    {
        if (isset($_POST[$this->getName()]))
        $this->setSetting('value', $_POST[$this->getName()]);
    }

    public function validateField($mandatory){

        if($mandatory == true && $this->getSetting('value') == ''){
            return 'Feld '.$this->_errorField.' ist ein Pflichtfeld, bitte ausfüllen';
        }
        if (!$this instanceof Vpc_Formular_Textarea_Index){
	        if ($this->getSetting('maxlength') < strlen($this->getSetting('value'))) return 'Feld '.$this->_errorField.' hat die Maximallänge überschritten';
        }

       return true;

    }
}
