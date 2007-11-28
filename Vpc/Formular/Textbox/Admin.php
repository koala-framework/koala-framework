<?php
class Vpc_Formular_Textbox_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['width'] = 'varchar(255) NOT NULL';
        $fields['value'] = 'varchar(255) NOT NULL';
        $fields['maxlength'] = 'smallint(6) NOT NULL default 50';
        $fields['validator'] = 'varchar(255) NOT NULL';
        $this->createFormTable('vpc_formular_textbox', $fields);
    }
}