<?php
class Vpc_Basic_Link_Mail_Admin extends Vpc_Basic_Link_Admin
{
    public function setup()
    {
        $fields['mail']     = "varchar(255) NOT NULL";
        $fields['subject']  = "varchar(255) NOT NULL";
        $fields['text']     = "text NOT NULL";
        $this->createFormTable('vpc_basic_link_mail', $fields);
    }
}