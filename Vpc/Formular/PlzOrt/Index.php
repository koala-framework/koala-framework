<?php
class Vpc_Formular_PlzOrt_Index extends Vpc_Formular_Field_Abstract
{
    protected $_plz;
    protected $_ort;

    protected $_defaultSettings = array('name' => '');

    function getTemplateVars()
    {
        $return['c1'] = $this->_plz->getTemplateVars('');
        $return['c2'] = $this->_ort->getTemplateVars('');
        $return['id'] = $this->getDbId().$this->getComponentKey();
        $return['template'] = 'Formular/PlzOrt.html';

        return $return;
    }

    public function setUp ()
    {
        $this->_plz = $this->createComponent('Vpc_Formular_Textbox_Index',$this->getDbId() , 1);
        $this->_ort = $this->createComponent('Vpc_Formular_Textbox_Index', $this->getDbId(), 2);
        $this->_plz->setSetting('width', 40);
        $this->_plz->setSetting('name', 'plz');
        $this->_plz->setSetting('maxlength', 4);
        $this->_ort->setSetting('width', 100);
        $this->_ort->setSetting('name', 'ort');
    }


    public function processInput()
    {
        if (isset($_POST[$this->getName().'plz'])){
           $this->_plz->setSetting('value', $_POST[$this->getName().'plz']);
        }
        if (isset($_POST[$this->getName().'ort'])){
           $this->_ort->setSetting('value', $_POST[$this->getName().'ort']);
        } else {

        }
    }

    public function validateField($mandatory)
    {
        $names = $this->getName();
        if (($_POST[$this->getName().'plz'] == '' ||  $_POST[$this->getName().'ort'] == '') && $mandatory == true){
            return 'Feld '.$this->_errorField.' ist ein Pflichtfeld, bitte alles ausfüllen';
        }
        return true;
    }


    public function setName($name)
    {
        $this->setSetting('name', $name);
        $this->_ort->setSetting('name', $name.'ort');
        $this->_plz->setSetting('name', $name.'plz');
    }


}