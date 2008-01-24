<?php
class Vpc_News_Categories_NewsToCategoriesModel extends Vps_Db_Table
{
    protected $_name = 'vpc_news_to_categories';

    protected $_referenceMap = array(
        'Vpc_News_Model' => array('columns' => 'news_id',
                                    'refTableClass' => 'Vpc_News_Model',
                                    'refColumns' => 'id'),
        'Vpc_News_Categories_Model'  => array('columns' => 'category_id',
                                    'refTableClass' => 'Vpc_News_Categories_Model',
                                    'refColumns' => 'id'));
}
