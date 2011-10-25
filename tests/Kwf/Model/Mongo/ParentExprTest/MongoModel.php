<?php
class Kwf_Model_Mongo_ParentExprTest_MongoModel extends Kwf_Model_Mongo_TestModel
{
    protected $_referenceMap = array(
        'Parent' => array(
            'refModelClass' => 'Kwf_Model_Mongo_ParentExprTest_ParentModel',
            'column' => 'parent_id'
        )
    );

    protected function _init()
    {
        parent::_init();
        $this->_exprs['parent_name'] = new Kwf_Model_Select_Expr_Parent('Parent', 'name');
    }
}
