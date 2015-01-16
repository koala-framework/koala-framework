<?php
class Kwc_Trl_StaticTextsForm_Translate_FrontendForm extends Kwf_Form
{
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        die('in test wird das formular nicht abgesendet, hier gehts nur um trl()!');
    }

    protected function _init()
    {
        $this->setModel(new Kwf_Model_FnF());

        $this->add(new Kwf_Form_Field_TextField('firstname', trlStatic('Firstname')));
        $this->add(new Kwf_Form_Field_TextField('lastname', trlStatic('Lastname')));
        $this->add(new Kwf_Form_Field_TextField('company', trlStatic('Company')));

        $this->add(new Kwf_Form_Field_TextField('firstname2', trlKwfStatic('Firstname')));
        $this->add(new Kwf_Form_Field_TextField('lastname2', trlKwfStatic('Lastname')));
        $this->add(new Kwf_Form_Field_TextField('company2', trlKwfStatic('Company')));

        $this->add(new Kwf_Form_Field_TextField('company3', trlKwfStatic('Company').'-'.trlKwfStatic('Lastname')));

        parent::_init();
    }
}
