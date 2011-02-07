<?php
class Vpc_Trl_NewsCategories_News_Category_Trl_CategoriesTestModel extends Vps_Model_FnF
{
    protected $_toStringField = 'name';
    protected $_primaryKey = 'component_id';
    public function __construct()
    {
        $data = array(
            array('component_id' => 'root-en_test-categories_1', 'name'=>'Lorem en', 'visible'=>1),
            array('component_id' => 'root-en_test-categories_2', 'name'=>'Ipsum en', 'visible'=>1),
        );
        $config = array(
            'data' => $data
        );
        parent::__construct($config);
    }
}
