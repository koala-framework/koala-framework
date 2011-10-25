<?php
class Kwf_Model_Mongo_ChildExprTest_MongoModel extends Kwf_Model_Mongo_TestModel
{
    protected $_dependentModels = array(
        'Child' => 'Kwf_Model_Mongo_ChildExprTest_ChildModel'
    );


    protected function _init()
    {
        parent::_init();
        $this->_exprs['child_count'] = new Kwf_Model_Select_Expr_Child_Count('Child');
    }
}
