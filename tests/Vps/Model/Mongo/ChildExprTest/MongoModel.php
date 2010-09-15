<?php
class Vps_Model_Mongo_ChildExprTest_MongoModel extends Vps_Model_Mongo_TestModel
{
    protected $_dependentModels = array(
        'Child' => 'Vps_Model_Mongo_ChildExprTest_ChildModel'
    );


    protected function _init()
    {
        parent::_init();
        $this->_exprs['child_count'] = new Vps_Model_Select_Expr_Child_Count('Child');
    }
}
