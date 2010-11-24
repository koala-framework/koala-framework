<?php
/**
 * @group ChildRows
 */
class Vps_Model_ChildRows_Test extends Vps_Test_TestCase
{
    public function testChildRows()
    {
        $cModel = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_ChildModel');
        $model = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_Model');
        $row = $model->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 2);

        $cRow = $row->createChildRow('Child');
        $cRow->bar = 'bar3';
        $cRow->save();

        $row = $model->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 3);

        $row = $cModel->getRow(3);
        $this->assertEquals($row->bar, 'bar3');
        $this->assertEquals($row->test_id, 1);

        $row = $model->getRow(1);
        $select = $model->select();
        $select->limit(1);
        $this->assertEquals(1, count($row->getChildRows('Child', $select)));

        $select = $model->select();
        $select->whereEquals('bar', 'bar2');
        $this->assertEquals(1, count($row->getChildRows('Child', $select)));
        $this->assertEquals('bar2', $row->getChildRows('Child', $select)->current()->bar);
    }


    public function testParentRow()
    {
        $cModel = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_ChildModel');
        $model = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_Model');
        $row = $cModel->getRow(1);
        $this->assertEquals($row->getParentRow('Parent')->id, 1);
        $this->assertEquals($row->getParentRow('Parent')->foo, 'foo1');
    }

    public function testToString()
    {
        $model = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_Model');
        $this->assertEquals('foo1', $model->getRow('1')->__toString());
    }
}
