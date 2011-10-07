<?php
class Vpc_Trl_NewsCategories_News_Trl_TestModel extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'root-en_test_1', 'visible'=>true, 'title'=>'loremen', 'teaser'=>'blah'),
            )
        );
        parent::__construct($config);
    }
}
