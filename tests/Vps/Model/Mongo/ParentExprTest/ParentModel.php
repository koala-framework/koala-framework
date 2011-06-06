<?php
class Vps_Model_Mongo_ParentExprTest_ParentModel extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'name'=>'one'),
        array('id'=>2, 'name'=>'two'),
        array('id'=>3, 'name'=>'three'),
    );

    protected $_dependentModels = array(
        'Mongo' => 'Vps_Model_Mongo_ParentExprTest_MongoModel'
    );
}
