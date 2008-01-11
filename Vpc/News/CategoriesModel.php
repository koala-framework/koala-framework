<?php
class Vpc_News_CategoriesModel extends Vps_Db_Table
{
    protected $_name = 'vpc_news_categories';
    protected $_rowClass = 'Vpc_News_CategoryRow';

    protected $_dependentTables = array('Vpc_News_NewsToCategoriesModel');
}
