<?php
class Vpc_Formular_Submit_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Template.html', 'Formular/Submit.html');

        $fields['text'] = 'varchar(255) NOT NULL';
        $this->createFormTable('vpc_formular_submit', $fields);
    }
}
