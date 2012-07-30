<?php
class Kwc_Directories_AjaxView_Category_Directory_ItemsToCategoriesModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'category_id'=>1, 'item_id'=>1),
    );
    protected $_referenceMap = array(
        'Item' => 'item_id->Kwc_Directories_AjaxView_Directory_Model',
        'Category' => 'category_id->Kwc_Directories_AjaxView_Category_Directory_CategoriesModel',
    );
}
