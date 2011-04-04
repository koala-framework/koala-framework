<?php
class Vpc_Directories_Category_ShowCategories_Model extends Vps_Model_Db
{
    protected $_table = 'vpc_directories_category_showcategories';

    protected $_referenceMap = array(
        'Category' => array(
            'column'        => 'category_id',
            'refModelClass' => 'Vpc_Directories_Category_Directory_CategoriesModel'
        )
    );
}
