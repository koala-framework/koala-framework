<?php
class Kwc_ArticlesCategory_Category_Directory_ArticlesToCategoriesModel extends Kwc_Articles_Category_Directory_ArticlesToCategoriesModel
{
    protected function _init()
    {
        parent::_init();
        $this->_referenceMap['Item']['refModelClass'] = 'Kwc_ArticlesCategory_Directory_Model';
    }
}
