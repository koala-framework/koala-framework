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
        ),array(
            'id' => 3, 'foo'=>'xxa', 'bar'=>'abcdef',
        ));
        $config['exprs'] = array();

        $config['exprs']['count_model2']
            = new Vps_Model_Select_Expr_Child_Count('Model2');

        $config['exprs']['count_model2_field']
            = new Vps_Model_Select_Expr_Child('Model2',
                    new Vps_Model_Select_Expr_Count('foo2'));

        $config['exprs']['count_model2_distinct']
            = new Vps_Model_Select_Expr_Child('Model2',
                    new Vps_Model_Select_Expr_Count('foo2', true));

        $config['exprs']['sum_model2']
            = new Vps_Model_Select_Expr_Child('Model2',
                    new Vps_Model_Select_Expr_Sum('foo2'));

        $select = new Vps_Model_Select();
        $select->whereEquals('bar', 'bam');
        $config['exprs']['count_model2_bam']
            = new Vps_Model_Select_Expr_Child('Model2',
                    new Vps_Model_Select_Expr_Count(),
                    $select);

        $config['exprs']['count_model2_bam_distinct']
            = new Vps_Model_Select_Expr_Child('Model2',
                    new Vps_Model_Select_Expr_Count('foo2', true),
                    $select);

        $config['exprs']['sum_model2_bam']
            = new Vps_Model_Select_Expr_Child('Model2',
                    new Vps_Model_Select_Expr_Sum('foo2'),
                    $select);

        $config['exprs']['contains_model2']
            = new Vps_Model_Select_Expr_Child_Contains('Model2');

        parent::__construct($config);
    }
}
