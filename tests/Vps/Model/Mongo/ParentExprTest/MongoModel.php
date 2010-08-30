<?php
class Vps_Model_Mongo_ParentExprTest_MongoModel extends Vps_Model_Mongo_TestModel
{
    protected $_referenceMap = array(
        'Parent' => array(
            'refModelClass' => 'Vps_Model_Mongo_ParentExprTest_ParentModel',
            'column' => 'parent_id'
        )
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['parent_name'] = new Vps_Model_Select_Expr_Parent('Parent', 'name');
    }
}
