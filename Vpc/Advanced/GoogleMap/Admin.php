<?php
class Vpc_Advanced_GoogleMap_Admin extends Vpc_Abstract_Composite_Admin
{
    public function setup()
    {
        parent::setup();

        $fields['coordinates'] = 'VARCHAR(40) NOT NULL';
        $fields['zoom'] = 'INT(11) NOT NULL';
        $fields['width'] = 'INT(11) NOT NULL';
        $fields['height'] = 'INT(11) NOT NULL';
        $fields['text'] = 'TEXT';
        $fields['zoom_properties'] = 'VARCHAR(255)';
        $fields['scale'] = 'TINYINT(2) NOT NULL';
        $fields['satelite'] = 'TINYINT(2) NOT NULL';
        $fields['overview'] = 'TINYINT(2) NOT NULL';

        $this->createFormTable('vpc_advanced_googlemap', $fields);
    }
}
