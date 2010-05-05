<?php
class Vps_AutoTree_Model extends Vps_Model_FnF {

    public function __construct($config = array())
    {
        $config['data'] = array(
            array('id'=>1, 'pos'=>1, 'parent_id' => null, 'name' => 'p1', 'search' => 'root'),
            array('id'=>2, 'pos'=>2, 'parent_id' => null, 'name' => 'p2', 'search' => 'root'),
            array('id'=>3, 'pos'=>1, 'parent_id' => 1, 'name' => 'p3', 'search' => 'l1'),
            array('id'=>4, 'pos'=>1, 'parent_id' => 3, 'name' => 'p4', 'search' => 'l2'),
            array('id'=>5, 'pos'=>1, 'parent_id' => 2, 'name' => 'p5', 'search' => 'l1'),
        );
        parent::__construct($config);
    }
}
