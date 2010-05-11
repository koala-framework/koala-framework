<?php
class Vps_Form_Field_SelectCountry extends Vps_Form_Field_Select
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);

        $this->setEditable(true);
        $this->setForceSelection(true);

        $file = Vps_Component_Abstract_Admin::getComponentFile($this, 'countries', 'xml');
        $xml = simplexml_load_file($file);
        $data = array();
        foreach ($xml->country as $country) {
            $data[] = array((string)$country->iso2, (string)$country->name);
        }
        $this->setValues($data);
    }
}
