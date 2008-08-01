<?php
class Vpc_News_Category_Directory_NewsToCategoriesModel
    extends Vpc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_name = 'vpc_news_to_categories';

    protected function _setup()
    {
        $this->_referenceMap['Item'] = array(
            'columns'           => array('news_id'),
            'refTableClass'     => 'Vpc_News_Directory_Model',
            'refColumns'        => array('id')
        );
        parent::_setup();
    }
}
