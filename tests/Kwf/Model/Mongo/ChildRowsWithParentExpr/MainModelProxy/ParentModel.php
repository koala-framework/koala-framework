<?php
class Kwf_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_ParentModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'name'=>'one'),
        array('id'=>2, 'name'=>'two'),
        array('id'=>3, 'name'=>'three'),
    );

    protected $_dependentModels = array(
        'MongoChild' => 'Kwf_Model_Mongo_ChildRowsWithParentExpr_MainModelProxy_MogoModel->Foo',
    );
}
