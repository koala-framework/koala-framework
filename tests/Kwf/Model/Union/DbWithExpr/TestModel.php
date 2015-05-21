<?php
class Kwf_Model_Union_DbWithExpr_TestModel extends Kwf_Model_Union_Db_TestModel
{
    protected $_models = array(
        '1m' => 'Kwf_Model_Union_DbWithExpr_Model1',
        '2m' => 'Kwf_Model_Union_DbWithExpr_Model2',
    );
    protected $_siblingModels = array('Kwf_Model_Union_DbWithExpr_ModelSibling');
}

