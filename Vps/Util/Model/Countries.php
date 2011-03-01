<?php
//TODO zusammenführen mit Vps_Util_Country
//TODO cachen wo sinnvoll
//TODO xml datei zum model schieben
class Vps_Util_Model_Countries extends Vps_Model_Data_Abstract
{
    protected $_rowClass = 'Vps_Util_Model_Row_Country';
    protected $_toStringField = 'name';

    public function getData()
    {
        if (!$this->_data) {
            $this->_data = array();
            $terr = Zend_Locale::getTranslationList(
                'Territory',
                Vps_Trl::getInstance()->getTargetLanguage(),
                '2'/*nur laender, ged aber ned korrekt mit zend 1.9*/);
            foreach ($terr as $iso2=>$terr) {
                if (is_numeric($iso2)) continue;
                if ($iso2=='ZZ') continue;
                $this->_data[] = array('id'=>(string)$iso2, 'name'=>(string)$terr);
            }
        }
        return $this->_data;
    }

    public function getNameByLanguageAndId($language, $id)
    {
        static $terr = array();
        if (!isset($terr[$language])) {
            $terr[$language] = Zend_Locale::getTranslationList(
                'Territory',
                $language,
                '2'/*nur laender, ged aber ned korrekt mit zend 1.9*/);
        }
        return $terr[$language][$id];
    }
}
