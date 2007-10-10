<?php
class Vpc_Basic_Text_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Index.html', 'Basic/Text.html');

        $fields['content'] = 'text NOT NULL';
        $this->createTable('vpc_basic_text', $fields);
    }
}