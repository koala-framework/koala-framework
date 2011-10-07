<?php
class Kwc_Basic_Space_Admin extends Kwc_Admin
{
    public function setup()
    {
        $fields['height'] = 'smallint(6) NOT NULL';
        $this->createFormTable('kwc_basic_space', $fields);
    }
}
