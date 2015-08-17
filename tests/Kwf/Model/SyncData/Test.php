<?php
/**
 * @group Model
 * @group Model_Tree
 */
class Kwf_Model_SyncData_Test extends Kwf_Test_TestCase
{
    private $_model;
    public function setUp()
    {
        parent::setUp();
        $this->_model = new Kwf_Model_FnF();
    }

    public function testSync()
    {
        $data = array(
            array('foo' => 'f1', 'bar' => 'b1'),
            array('foo' => 'f2', 'bar' => 'b2')
        );
        $this->_model->syncData($data, array('foo'));
        $this->assertEquals(2, $this->_model->countRows());
        $this->assertEquals('f1', $this->_model->getRow(1)->foo);

        $data = array(
            array('foo' => 'f2', 'bar' => 'b2a'),
            array('foo' => 'f3', 'bar' => 'b3')
        );
        $this->_model->syncData($data, array('foo'));
        $this->assertEquals(2, $this->_model->countRows());
        $this->assertEquals('b2a', $this->_model->getRow(2)->bar);
        $this->assertEquals('f3', $this->_model->getRow(3)->foo);
    }

    public function testSyncWithSelect()
    {
        $data = array(
            array('foo' => 'f1', 'bar' => 'b1', 'baz' => 'a'),
            array('foo' => 'f2', 'bar' => 'b2', 'baz' => 'b')
        );
        $this->_model->syncData($data, array('foo'));

        $data = array(
            array('foo' => 'f1', 'bar' => 'b1a', 'baz' => 'a'),
            array('foo' => 'f3', 'bar' => 'b3', 'baz' => 'a')
        );
        $select = $this->_model->select()->whereEquals('baz', 'a');
        $this->_model->syncData($data, array('foo'), $select);
        $this->assertEquals(3, $this->_model->countRows());
        $this->assertEquals('b1a', $this->_model->getRow(1)->bar);
        $this->assertEquals('f3', $this->_model->getRow(3)->foo);
    }

    public function testMapping()
    {
        $data = array(
            array('foo' => 'f1', 'bar' => 'b1'),
            array('foo' => 'f2', 'bar' => 'b2')
        );
        $this->_model->syncData($data, array('foo'));

        $data = array(
            'i1' => array('foo' => 'f2', 'bar' => 'b2a'),
            'i2' => array('foo' => 'f3', 'bar' => 'b3')
        );
        $result = $this->_model->syncData($data, array('foo'));
        $this->assertEquals(2, $result['mapping']['i1']);
        $this->assertEquals(3, $result['mapping']['i2']);
    }
}
