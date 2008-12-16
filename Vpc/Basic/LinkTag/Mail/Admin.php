<?php
class Vpc_Basic_LinkTag_Mail_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
{
    public function setup()
    {
        $fields['mail']     = "varchar(255) NOT NULL";
        $fields['subject']  = "varchar(255) NOT NULL";
        $fields['text']     = "text NOT NULL";
        $this->createFormTable('vpc_basic_link_mail', $fields);
    }
}
