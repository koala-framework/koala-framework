<?php
class Vpc_Basic_Link_Intern_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['target']   = "varchar(255) NOT NULL";
        $fields['rel']      = "varchar(255) NOT NULL";
        $fields['param']    = "varchar(255) NOT NULL";
        $this->createFormTable('vpc_basic_link_intern', $fields);
    }
}
