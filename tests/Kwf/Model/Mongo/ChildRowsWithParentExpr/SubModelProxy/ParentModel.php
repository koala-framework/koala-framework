<?php
class Vps_Model_Mongo_ChildRowsWithParentExpr_SubModelProxy_ParentModel extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'name'=>'one'),
        array('id'=>2, 'name'=>'two'),
        array('id'=>3, 'name'=>'three'),
    );

    protected $_dependentModels = array(
        'MongoChild' => 'Vps_Model_Mongo_ChildRowsWithParentExpr_SubModelProxy_MongoModel->Foo',
    );
}
