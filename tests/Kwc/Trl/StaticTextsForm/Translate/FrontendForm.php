<?php
class Vpc_Trl_StaticTextsForm_Translate_FrontendForm extends Vps_Form
{
    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        die('in test wird das formular nicht abgesendet, hier gehts nur um trl()!');
    }

    protected function _init()
    {
        $this->setModel(new Vps_Model_FnF());

        $this->add(new Vps_Form_Field_TextField('firstname', trlStatic('Vorname')));
        $this->add(new Vps_Form_Field_TextField('lastname', trlStatic('Nachname')));
        $this->add(new Vps_Form_Field_TextField('company', trlStatic('Firma')));

        $this->add(new Vps_Form_Field_TextField('firstname2', trlVpsStatic('Firstname')));
        $this->add(new Vps_Form_Field_TextField('lastname2', trlVpsStatic('Lastname')));
        $this->add(new Vps_Form_Field_TextField('company2', trlVpsStatic('Company')));

        $this->add(new Vps_Form_Field_TextField('company3', trlVpsStatic('Company').'-'.trlVpsStatic('Lastname')));

        parent::_init();
    }
}
