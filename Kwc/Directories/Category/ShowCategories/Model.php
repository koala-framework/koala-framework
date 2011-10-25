<?php
class Kwc_Directories_Category_ShowCategories_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_directories_category_showcategories';

    protected $_referenceMap = array(
        'Category' => array(
            'column'        => 'category_id',
            'refModelClass' => 'Kwc_Directories_Category_Directory_CategoriesModel'
        )
    );
}
