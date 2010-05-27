<?php
class Vpc_Trl_NewsCategories_News_Category_CategoriesTestModel extends Vps_Model_FnF
{
    protected $_toStringField = 'name';
    public function __construct()
    {
        $data = array(
            array('id'=>'1', 'name'=>'Lorem'),
            array('id'=>'2', 'name'=>'Ipsum'),
        );
        $config = array(
            'data' => $data
        );
        parent::__construct($config);
    }
}
