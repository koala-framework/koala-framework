<?php
class Kwc_Blog_Category_Directory_BlogPostsToCategoriesModel
    extends Kwc_Directories_Category_Directory_ItemsToCategoriesModel
{
    protected $_table = 'kwc_blog_posts_to_categories';

    protected function _init()
    {
        $this->_referenceMap['Item'] = array(
            'column'           => 'blog_post_id',
            'refModelClass'     => 'Kwc_Blog_Directory_Model'
        );
        parent::_init();
    }
}
