<?php
class Kwf_Model_Union_DbWithExpr_ModelSibling extends Kwf_Model_Union_Db_ModelSibling
{
    protected $_referenceMap = array(
        'Master' => array(
            'column' => 'id',
            'refModelClass' => 'Kwf_Model_Union_DbWithExpr_TestModel'
        )
    );
}
