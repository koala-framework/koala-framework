<?php
class Kwf_Model_Union_DeletedFlag_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DeletedFlag_TestModel');
    }

    public function testDeleteRow()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DeletedFlag_TestModel');
        Kwf_Events_Dispatcher::addListeners($model);
        $this->assertEquals($model->countRows(), 2);

        $m1 = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DeletedFlag_Model1');
        $m1->getRow(1)->delete();

        $this->assertEquals($model->countRows(), 1);
    }
}