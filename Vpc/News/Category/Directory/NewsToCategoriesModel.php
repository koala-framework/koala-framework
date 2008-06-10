<?php
class Vpc_News_Category_Directory_NewsToCategoriesModel extends Vps_Db_Table_Abstract
{
    protected $_name = 'vpc_news_to_categories';
    protected $_referenceMap = array(
        'Category' => array(
            'columns'           => array('category_id'),
            'refTableClass'     => 'Vps_Dao_Pool',
            'refColumns'        => array('id')
        ),
        'News' => array(
            'columns'           => array('news_id'),
            'refTableClass'     => 'Vpc_News_Directory_Model',
            'refColumns'        => array('id')
        )
    );
}
