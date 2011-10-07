<?php
class Kwc_Mail_Admin extends Kwc_Admin
{
    public function setup()
    {
        $fields['subject'] = 'VARCHAR(255) NOT NULL';
        $fields['from_email'] = 'VARCHAR(255) NOT NULL';
        $fields['from_name'] = 'VARCHAR(255) NOT NULL';
        $fields['reply_email'] = 'VARCHAR(255) NOT NULL';
        $this->createFormTable('kwc_mail', $fields);
    }
}
