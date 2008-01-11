<?php
class Vpc_News_NewsToCategoriesModel extends Vps_Db_Table
{
    protected $_name = 'vpc_news_to_categories';

    protected $_referenceMap = array(
        'Vps_News_Model' => array('columns' => 'news_id',
                                    'refTableClass' => 'Vpc_News_Model',
                                    'refColumns' => 'id'),
        'Vps_News_CategoriesModel'  => array('columns' => 'category_id',
                                    'refTableClass' => 'Vpc_News_CategoriesModel',
                                    'refColumns' => 'id'));
}
