<?php
class Vpc_Formular_Checkbox_Component extends Vpc_Formular_Field_Abstract
{
    protected $_settings = array(
        'text' => '',
        'checked' => false,
        'value' => '',
        'name' => ''
    );
    protected $_tablename = 'Vpc_Formular_Checkbox_Model';
    const NAME = 'Formular.Checkbox';


    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['value'] = $this->getSetting('value');
        $return['checked'] = $this->getSetting('checked');
        $return['text'] = $this->getSetting('text');
        $return['name'] = $this->getSetting('name');
        $return['template'] = 'Formular/Checkbox.html';
        return $return;
    }

    public function processInput(){
        if (isset($_POST[$this->getSetting('name')])) {
            $this->setSetting('checked', 1);
            $check = $this->getSetting('name');
            if ($this instanceof  Vpc_Formular_Option_Component) {
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
            return 'Feld ' . $this->getStore('description') . ' ist ein Pflichtfeld, bitte ausfüllen';
        }
        return '';
    }
}
