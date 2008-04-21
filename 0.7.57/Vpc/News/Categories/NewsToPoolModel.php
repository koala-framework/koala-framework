<?php
class Vpc_News_Categories_NewsToPoolModel extends Vps_Db_Table
{
    protected $_name = 'vpc_news_to_categories';

    protected $_referenceMap = array(
        'Vpc_News_Model' => array('columns' => 'news_id',
                                    'refTableClass' => 'Vpc_News_Model',
                                    'refColumns' => 'id'),
        'Vps_Dao_Pool'  => array('columns' => 'category_id',
                                    'refTableClass' => 'Vps_Dao_Pool',
                                    'refColumns' => 'id'));
}
