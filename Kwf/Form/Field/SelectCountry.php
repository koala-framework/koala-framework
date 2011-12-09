<?php
/**
 * @package Form
 */
class Kwf_Form_Field_SelectCountry extends Kwf_Form_Field_Select
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
        if (!$language) $language = Kwf_Trl::getInstance()->getTargetLanguage();
        $nameColumn = 'name_'.$language;
        foreach (Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Countries')->getRows() as $row) {
            $values[$row->id] = $row->$nameColumn;
        }
        asort($values, SORT_LOCALE_STRING);
        $this->setValues($values);
    }
}
