<?php
class Vpc_Mail_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['subject'] = 'VARCHAR(255) NOT NULL';
        $this->createFormTable('vpc_mail', $fields);
    }
}
