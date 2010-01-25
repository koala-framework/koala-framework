<?php
class Vpc_NewsCategory_Category_Directory_NewsToCategoriesModel
    extends Vpc_News_Category_Directory_NewsToCategoriesModel
{
    protected function _init()
    {
        parent::_init();
        $this->_referenceMap['Item'] = array(
            'column'           => 'news_id',
            'refModelClass'     => 'Vpc_NewsCategory_Model'
        );
    }
}
