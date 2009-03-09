<?php
class Vps_Model_Tree_TestModel extends Vps_Model_Tree
{
    protected $_toStringField = 'name';
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'parent_id'=>null, 'name'=>'root'),
                array('id'=>2, 'parent_id'=>1, 'name'=>'child1'),
                array('id'=>3, 'parent_id'=>1, 'name'=>'child2'),
                array('id'=>4, 'parent_id'=>1, 'name'=>'child3'),
                array('id'=>5, 'parent_id'=>2, 'name'=>'child1.1'),
                array('id'=>6, 'parent_id'=>2, 'name'=>'child1.2'),
                array('id'=>7, 'parent_id'=>5, 'name'=>'child1.1.1'),
            )
        ));
        parent::__construct($config);
    }
}
