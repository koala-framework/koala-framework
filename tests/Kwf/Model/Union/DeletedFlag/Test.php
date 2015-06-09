<?php
class Kwf_Model_Union_DeletedFlag_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DeletedFlag_TestModel');
        Kwf_Events_Dispatcher::addListeners('Kwf_Model_Union_DeletedFlag_TestModel');
    }

    public function testDeleteRow1()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DeletedFlag_TestModel');
        $this->assertEquals($model->countRows(), 2);

        $m1 = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DeletedFlag_Model1');
        $m1->getRow(1)->delete();

        $this->assertEquals($model->countRows(), 1);
    }

    public function testDeleteRow2()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DeletedFlag_TestModel');
        $this->assertEquals($model->countRows(), 2);
        $model->getRow('1m1')->delete();
        $this->assertEquals($model->countRows(), 1);
    }

    public function testIgnoreDeleted()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DeletedFlag_TestModel');
        $s = new Kwf_Model_Select();
        $s->ignoreDeleted();
        $this->assertEquals($model->countRows($s), 3);
    }
}
