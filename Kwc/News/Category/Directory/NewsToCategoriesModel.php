<?php
class Kwc_News_Category_Directory_NewsToCategoriesModel
    extends Kwc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_table = 'kwc_news_to_categories';

    protected function _init()
    {
        $this->_referenceMap['Item'] = array(
            'column'           => 'news_id',
            'refModelClass'     => 'Kwc_News_Directory_Model'
        );
        parent::_init();
    }
}
