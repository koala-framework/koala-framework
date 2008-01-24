<?php
class Vpc_News_Categories_Model extends Vps_Db_Table
{
    protected $_name = 'vpc_news_categories';
    protected $_rowClass = 'Vpc_News_Categories_Row';

    protected $_dependentTables = array('Vpc_News_Categories_NewsToCategoriesModel');
}
