<?php
class Vpc_News_Model extends Vpc_Table
{
    protected $_name = 'vpc_news';
    protected $_rowClass = 'Vpc_News_Row';

    protected $_dependentTables = array('Vpc_News_NewsToCategoriesModel');
}
