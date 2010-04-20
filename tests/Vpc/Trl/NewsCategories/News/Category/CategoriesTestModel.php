<?php
class Vpc_Trl_NewsCategories_News_Category_CategoriesTestModel extends Vps_Model_FnF
{
    public function __construct()
    {
        $data = array(
            array('id'=>'1', 'value'=>'Lorem'),
            array('id'=>'2', 'value'=>'Ipsum'),
        );
        $config = array(
            'data' => $data
        );
        parent::__construct($config);
    }
}
