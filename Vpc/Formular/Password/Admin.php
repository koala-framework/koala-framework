<?php
class Vpc_Formular_Password_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Template.html', 'Formular/Password.html');

        $fields['maxlength'] = 'smallint (6) NOT NULL';
        $fields['width'] = 'smallint(6) NOT NULL';
        $this->createTable("vpc_formular_password", $fields);
    }
}