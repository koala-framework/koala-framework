<?php
class Vpc_Formular_Email_Index extends Vpc_Formular_Field_Simple_Abstract
{
    protected $_defaultSettings = array('maxlength' => '255', 'width' => '20', 'name' => '', 'value' => '');

    public function getTemplateVars($mode)
    {
        $return['maxlength'] = $this->getSetting('maxlength');
        $return['width'] = $this->getSetting('width');
        $return['name'] = $this->getSetting('name');
        $return['value'] = $this->getSetting('value');
        $return['id'] = $this->getComponentId();
        $return['template'] = 'Formular/Email.html';
        return $return;
    }

    public function validateField($mandatory)
    {
        if ($this->getSetting('value') != ''){
	        $validator = new Zend_Validate_EmailAddress();
	        if (!$validator->isValid($this->getSetting('value'))) return 'Die von Ihnen angegebene Emailadresse ist nicht korrekt';
        }
        return parent::validateField($mandatory);
    }
}