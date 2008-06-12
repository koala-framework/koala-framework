<?php
class Vpc_News_Top_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['news_component_id'] = 'varchar(255) NOT NULL';
        $this->createFormTable('vpc_news_top', $fields);
    }
}
