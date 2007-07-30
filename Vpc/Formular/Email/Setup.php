<?php
class Vpc_Formular_Email_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $fields['value'] = 'varchar(255) NOT NULL';
        $fields['width'] = 'smallint(6) NOT NULL';
        $fields['maxlength'] = 'varchar(6) NOT NULL';
        $this->createTable('component_formular_email', $fields);
    }
}