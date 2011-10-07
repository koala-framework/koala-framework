<?php
class Vpc_News_Category_Directory_NewsToCategoriesModel
    extends Vpc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_table = 'vpc_news_to_categories';

    protected function _init()
    {
        $this->_referenceMap['Item'] = array(
            'column'           => 'news_id',
            'refModelClass'     => 'Vpc_News_Directory_Model'
        );
        parent::_init();
    }
}
