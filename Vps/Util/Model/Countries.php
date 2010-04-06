<?php
//TODO zusammenführen mit Vps_Util_Country und Vps_Form_Field_SelectCountry
//TODO cachen wo sinnvoll
//TODO xml datei zum model schieben
class Vps_Util_Model_Countries extends Vps_Model_Data_Abstract
{
    public function getData()
    {
        if (!$this->_data) {
            $this->_data = array();
            $xml = simplexml_load_file(VPS_PATH . '/Vps/Form/Field/SelectCountry/countries.xml');
            foreach ($xml->country as $country) {
                $this->_data[] = array('id'=>(string)$country->iso2, 'name'=>(string)$country->name);
            }

        }
        return $this->_data;
    }
}
