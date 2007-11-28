<?php
class Vpc_Formular_PlzOrt_Component extends Vpc_Formular_Field_Abstract
{
    protected $_plz;
    protected $_ort;
    protected $_settings = array('name' => '');
    const NAME = 'Formular.PlzOrt';

    function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['c1'] = $this->_plz->getTemplateVars('');
        $return['c2'] = $this->_ort->getTemplateVars('');
        $return['template'] = 'Formular/PlzOrt.html';
        return $return;
    }

    public function init ()
    {
        $this->_plz = $this->createComponent('Vpc_Formular_Textbox_Component', 1);
        $this->_plz->setSetting('name', 'plz');
        $this->_plz->setSetting('width', 40);
        $this->_plz->setSetting('maxlength', 4);

        $this->_ort = $this->createComponent('Vpc_Formular_Textbox_Component', 2);
        $this->_ort->setSetting('name', 'ort');
        $this->_ort->setSetting('width', 100);
    }

    public function processInput()
    {
        $this->_plz->processInput();
        $this->_ort->processInput();
    }

    public function validateField($mandatory)
    {
        $return = '';
        $return .= $this->_plz->validateField($mandatory);
        $return .= $this->_ort->validateField($mandatory);
        return $return;
    }

}