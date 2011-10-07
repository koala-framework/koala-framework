<?php
class Kwc_Basic_Html_Admin extends Kwc_Admin
{
    public function setup()
    {
        $fields['content'] = 'text NOT NULL';
        $this->createFormTable('kwc_basic_html', $fields);
    }
}
