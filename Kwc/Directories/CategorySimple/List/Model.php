<?php
class Kwc_Directories_CategorySimple_List_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_directory_categories_to_components';
    protected $_referenceMap = array(
        'Category' => 'category_id->Kwc_Directories_CategorySimple_CategoriesModel',
    );
}
