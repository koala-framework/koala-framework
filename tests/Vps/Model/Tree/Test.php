<?php
/**
 * @group Model_Tree
 */
class Vps_Model_Tree_Test extends PHPUnit_Framework_TestCase
{
    private $_model;
    public function setUp()
    {
        $this->_model = new Vps_Model_Tree_TestModel();
    }

    public function testPath()
    {
        $row = $this->_model->getRow(7);
        $this->assertEquals('child1.1.1', $row->__toString());

        $this->assertEquals('root-child1-child1.1-child1.1.1', $row->getTreePath('-'));

        $row = $this->_model->getRow(3);
        $this->assertEquals('root-child2', $row->getTreePath('-'));
    }
    public function testChildCategoryIds()
    {
        $row = $this->_model->getRow(7);
        $this->assertEquals(array(), $row->getRecursiveChildIds());

        $row = $this->_model->getRow(3);
        $this->assertEquals(array(), $row->getRecursiveChildIds());

        $row = $this->_model->getRow(6);
        $this->assertEquals(array(), $row->getRecursiveChildIds());

        $row = $this->_model->getRow(5);
        $this->assertEquals(array(7), $row->getRecursiveChildIds());

        $row = $this->_model->getRow(4);
        $this->assertEquals(array(), $row->getRecursiveChildIds());
        
        $row = $this->_model->getRow(3);
        $this->assertEquals(array(), $row->getRecursiveChildIds());
        
        $row = $this->_model->getRow(2);
        $ids = $row->getRecursiveChildIds();
        asort($ids);
        $this->assertEquals(array(5,6,7), array_values($ids));

        $row = $this->_model->getRow(1);
        $ids = $row->getRecursiveChildIds();
        asort($ids);
        $this->assertEquals(array(2,3,4,5,6,7), array_values($ids));
    }
}
