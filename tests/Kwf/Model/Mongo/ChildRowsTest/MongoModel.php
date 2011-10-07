<?php
class Kwf_Model_Mongo_ChildRowsTest_MongoModel extends Kwf_Model_Mongo_TestModel
{
    protected function _init()
    {
        parent::_init();
        $this->_dependentModels['Foo'] = new Kwf_Model_Mongo_RowsSubModel(array(
            'fieldName' => 'foo',
            'parentModel' => $this
        ));
    }
}
