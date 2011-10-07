<?php
class Kwf_Model_Mongo_ParentExprTest_ParentModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'name'=>'one'),
        array('id'=>2, 'name'=>'two'),
        array('id'=>3, 'name'=>'three'),
    );

    protected $_dependentModels = array(
        'Mongo' => 'Kwf_Model_Mongo_ParentExprTest_MongoModel'
    );
}
