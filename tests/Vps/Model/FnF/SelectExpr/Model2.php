<?php
class Vps_Model_FnF_SelectExpr_Model2 extends Vps_Model_FnF
{
    protected $_referenceMap = array(
        'Model1' => array(
            'column' => 'model1_id',
            'refModelClass' => 'Vps_Model_FnF_SelectExpr_Model1'
        )
    );
    public function __construct($config = array())
    {
        $config['data'] = array(array(
            'id' => 1, 'model1_id'=>1, 'foo2'=>10, 'bar' => 'abcd',
        ),array(
            'id' => 2, 'model1_id'=>1, 'foo2'=>10, 'bar' => 'bam',
        ),array(
            'id' => 3, 'model1_id'=>1, 'foo2'=>null, 'bar' => 'bam',
        ),array(
            'id' => 4, 'model1_id'=>2, 'foo2'=>10, 'bar' => 'bam',
        ));
        parent::__construct($config);
    }
}
