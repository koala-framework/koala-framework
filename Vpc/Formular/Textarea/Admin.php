<?php
class Vpc_Formular_Textarea_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Index.html', 'Formular/Textarea.html');

        $fields['cols'] = 'smallint(6) NOT NULL';
        $fields['rows'] = 'smallint(6) NOT NULL';
        $this->createTable('vpc_formular_textarea', $fields);
    }
}