<?php
class Vps_Model_Mongo_ChildExprWithProxyTest_ChildModel extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'mongo_id'=>1, 'name'=>'one'),
        array('id'=>2, 'mongo_id'=>1, 'name'=>'two'),
        array('id'=>3, 'mongo_id'=>2, 'name'=>'three'),
    );

    protected $_referenceMap = array(
        'Mongo' => array(
            'refModelClass' => 'Vps_Model_Mongo_ChildExprWithProxyTest_MongoModel',
            'column' => 'mongo_id'
        )
    );
}
