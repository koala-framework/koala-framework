<?php
class Vps_Model_Mongo_ChildRowsTest_MongoModel extends Vps_Model_Mongo_TestModel
{
    protected function _init()
    {
        parent::_init();
        $this->_dependentModels['Foo'] = new Vps_Model_Mongo_RowsSubModel(array(
            'fieldName' => 'foo',
            'parentModel' => $this
        ));
    }
}
