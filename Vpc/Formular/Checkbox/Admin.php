<?php
class Vpc_Formular_Checkbox_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Index.html', 'Formular/Checkbox.html');

        $fields['value'] = 'varchar(255) NOT NULL';
        $fields['text'] = 'varchar(255) NOT NULL';
        $fields['checked'] = 'tinyint(4) NOT NULL';
        $this->createTable('vpc_formular_checkbox', $fields);
    }
}