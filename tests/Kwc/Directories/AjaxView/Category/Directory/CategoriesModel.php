<?php
class Kwc_Directories_AjaxView_Category_Directory_CategoriesModel extends Kwf_Model_FnF
{
    protected $_toStringField = 'name';
    protected $_columns = array('id', 'name');
    protected $_data = array(
        array('id'=>1, 'name'=>'Cat1'),
        array('id'=>2, 'name'=>'Cat2'),
    );
}
