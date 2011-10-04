<?php
class Vps_Form_Field_SelectCountry extends Vps_Form_Field_Select
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);

        $this->setEditable(true);
        $this->setForceSelection(true);
    }

    public function trlStaticExecute($language = null)
    {
        parent::trlStaticExecute($language);

        $values = array();
        if (!$language) $language = Vps_Trl::getInstance()->getTargetLanguage();
        $nameColumn = 'name_'.$language;
        foreach (Vps_Model_Abstract::getInstance('Vps_Util_Model_Countries')->getRows() as $row) {
            $values[$row->id] = $row->$nameColumn;
        }
        asort($values, SORT_LOCALE_STRING);
        $this->setValues($values);
    }
}
