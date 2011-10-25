<?php
class Kwc_NewsCategory_Category_Directory_NewsToCategoriesModel
    extends Kwc_News_Category_Directory_NewsToCategoriesModel
{
    protected function _init()
    {
        parent::_init();
        $this->_referenceMap['Item'] = array(
            'column'           => 'news_id',
            'refModelClass'     => 'Kwc_NewsCategory_Model'
        );
    }
}
