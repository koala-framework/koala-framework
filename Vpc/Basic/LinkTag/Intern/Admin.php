<?php
class Vpc_Basic_LinkTag_Intern_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
{
    public function setup()
    {
        $fields['target']   = "varchar(255) NOT NULL";
        $this->createFormTable('vpc_basic_link_intern', $fields);
    }
}
