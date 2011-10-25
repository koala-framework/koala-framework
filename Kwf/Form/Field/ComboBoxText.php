<?php
/**
 * Text-Eingabefeld mit Autocomplete
 * Gespeichert wird nicht wie bei ComboBox bzw. Select die ID vom ausgewählten
 * Datensatz, sondern der eingegebene Text. ComboBox ist nur Ausfüllhilfe.
 **/
class Kwf_Form_Field_ComboBoxText extends Kwf_Form_Field_ComboBox
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setValueField(false);
        $this->setTriggerAction('all');
        $this->setCtCls('comboboxtext');
    }

    protected function _addValidators()
    {
        Kwf_Form_Field_SimpleAbstract::_addValidators();
    }

}
