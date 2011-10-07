<?php
class Kwc_Trl_News_News_Trl_TestModel extends Kwf_Model_FnF
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
