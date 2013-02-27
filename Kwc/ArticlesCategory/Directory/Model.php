<?php
class Kwc_ArticlesCategory_Directory_Model extends Kwc_Articles_Directory_Model
{
    protected function _init()
    {
        parent::_init();
        $this->_dependentModels['Categories'] = 'Kwc_ArticlesCategory_Category_Directory_ArticlesToCategoriesModel';
    }
}
