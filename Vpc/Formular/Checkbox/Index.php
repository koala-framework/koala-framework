<?php
class Vpc_Formular_Checkbox_Index extends Vpc_Formular_Field_Abstract
{
    protected $_defaultSettings = array('text' => '', 'checked' => false, 'value' => '', 'name' => '');

    public function getTemplateVars()
    {
        $name = $this->getSetting('name');
        $value = $this->getSetting('value');
        $text = $this->getSetting('text');
        $checked = $this->getSetting('checked');

        $return['value'] = $value;
        $return['checked'] = $checked;
        $return['name'] = $name;
        $return['text'] = $text;
        $return['id'] = $this->getComponentId();
        $return['template'] = 'Formular/Checkbox.html';
        return $return;
    }

    public function processInput(){
        if (isset($_POST[$this->getSetting('name')])) {
            $this->setSetting('checked', 1);
            $check = $this->getSetting('name');
            if ($this instanceof  Vpc_Formular_Option_Index) {

                if ($this->getSetting('value') == $_POST[$this->getSetting('name')]) {
                    $this->setSetting('checked', 1);
                } else {
                    $this->setSetting('checked', 0);
                }
            }
        } else {
            $this->setSetting('checked', 0);
            $check = $this->getSetting('name');
        }
    }

    public function validateField($mandatory)
    {
        if ($mandatory && !$this->getSetting('checked')) {
            return 'Feld '.$this->_errorField.' ist ein Pflichtfeld, bitte ausfüllen';
        } else {
            return true;
        }
    }
}
