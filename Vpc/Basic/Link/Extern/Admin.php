<?php
class Vpc_Basic_Link_Extern_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['target']       = "varchar(255) NOT NULL";
        $fields['rel']          = "varchar(255) NOT NULL";
        $fields['param']        = "varchar(255) NOT NULL";
        $fields['is_popup']     = "tinyint(4) NOT NULL";
        $fields['width']        = "mediumint DEFAULT NULL";
        $fields['height']       = "mediumint DEFAULT NULL";
        $fields['menubar']      = "tinyint(4) NOT NULL";
        $fields['toolbar']      = "tinyint(4) NOT NULL";
        $fields['locationbar']  = "tinyint(4) NOT NULL";
        $fields['statusbar']    = "tinyint(4) NOT NULL";
        $fields['scrollbars']   = "tinyint(4) NOT NULL";
        $fields['resizeable']   = "tinyint(4) NOT NULL";
        $this->createFormTable('vpc_basic_link_extern', $fields);
    }
}