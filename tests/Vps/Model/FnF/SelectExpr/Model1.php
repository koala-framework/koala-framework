<?php
class Vps_Model_FnF_SelectExpr_Model1 extends Vps_Model_FnF
{
    protected $_dependentModels = array(
        'Model2' => 'Vps_Model_FnF_SelectExpr_Model2'
    );
    public function __construct($config = array())
    {
        $config['data'] = array(array(
            'id' => 1, 'foo'=>'xxy', 'bar'=>'abc',
        ),array(
            'id' => 2, 'foo'=>'xxz', 'bar'=>'abcde',
        ));
        $config['exprs'] = array(
            'count_model2' => new Vps_Model_Select_Expr_Child_Count('Model2')
        );
        parent::__construct($config);
    }
}
