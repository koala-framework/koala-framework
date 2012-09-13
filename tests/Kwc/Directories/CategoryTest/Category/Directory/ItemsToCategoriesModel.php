<?php
class Kwc_Directories_CategoryTest_Category_Directory_ItemsToCategoriesModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'category_id'=>1, 'item_id'=>1),
        array('id'=>2, 'category_id'=>2, 'item_id'=>2),
    );
    protected $_referenceMap = array(
        'Item' => 'item_id->Kwc_Directories_CategoryTest_Directory_Model',
        'Category' => 'category_id->Kwc_Directories_CategoryTest_Category_Directory_CategoriesModel',
    );
}
