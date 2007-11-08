<?php
class Vpc_Basic_Html_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['content'] = 'text NOT NULL';
        $this->createTable('vpc_basic_html', $fields);
    }
}