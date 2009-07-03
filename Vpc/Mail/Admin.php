<?php
class Vpc_Mail_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['subject'] = 'VARCHAR(255) NOT NULL';
        $fields['from_email'] = 'VARCHAR(255) NOT NULL';
        $fields['from_name'] = 'VARCHAR(255) NOT NULL';
        $fields['reply_email'] = 'VARCHAR(255) NOT NULL';
        $this->createFormTable('vpc_mail', $fields);
    }
}
