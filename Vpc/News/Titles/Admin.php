<?php
class Vpc_News_Titles_Admin extends Vpc_Admin
{
    public function setup()
    {
        $fields['news_component_id'] = 'varchar(255) NULL';
        $this->createFormTable('vpc_news_title_paragraphs', $fields);
    }
}
