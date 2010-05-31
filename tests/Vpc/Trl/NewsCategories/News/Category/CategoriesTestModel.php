<?php
class Vpc_Trl_NewsCategories_News_Category_CategoriesTestModel extends Vps_Model_FnF
{
    protected $_toStringField = 'name';
    public function __construct()
    {
        $data = array(
            array('id'=>'1', 'name'=>'Lorem', 'component_id' => 'root-master_test-categories', 'visible'=>1),
            array('id'=>'2', 'name'=>'Ipsum', 'component_id' => 'root-master_test-categories', 'visible'=>1),
            array('id'=>'3', 'name'=>'Dolor', 'component_id' => 'root-master_test-categories', 'visible'=>1),
        );
        $config = array(
            'data' => $data
        );
        parent::__construct($config);
    }
}
