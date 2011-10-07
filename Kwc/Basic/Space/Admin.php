<?php
class Vpc_Basic_Space_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['height'] = 'smallint(6) NOT NULL';
        $this->createFormTable('vpc_basic_space', $fields);
    }
}
