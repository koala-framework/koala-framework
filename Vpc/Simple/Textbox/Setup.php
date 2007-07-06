<?php
class Vpc_Simple_Textbox_Setup
{
    public static function getConfigParams()
    {
        return array(
            array('type'       => 'fieldset',
                  'legend'     => 'Textbox'),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Breite der Textbox',
                  'name'       => 'width',
                  'width'      => 50),
            array('type'       => 'TextField',
                  'fieldLabel' => 'Höhe der Textbox',
                  'name'       => 'height',
                  'width'      => 50),
            array('type'       => 'end'),
        );
    }

}
