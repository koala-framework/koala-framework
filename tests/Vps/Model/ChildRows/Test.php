<?php
/**
 * @group Model
 * @group ChildRows
 */
class Vps_Model_ChildRows_Test extends Vps_Test_TestCase
{
    public function setUp()
    {
        Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_ChildModel')->setData(array(
            array('id'=>1, 'test_id'=>1, 'bar'=>'bar1'),
            array('id'=>2, 'test_id'=>1, 'bar'=>'bar2')
        ));
        Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_Model')->setData(array(
            array('id'=>1, 'foo'=>'foo1'),
            array('id'=>2, 'foo'=>'foo2')
        ));
    }
    public function testChildRows()
    {
        $cModel = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_ChildModel');
        $model = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_Model');
        $row = $model->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 2);

        $rows = $row->getChildRows($cModel); // nicht per Rule, sonder direkt Model
        $this->assertEquals(count($rows), 2);

        $cRow = $row->createChildRow('Child');
        $cRow->bar = 'bar3';
        $cRow->save();

        $cRow = $row->createChildRow($cModel); // nicht per Rule, sonder direkt Model
        $cRow->bar = 'bar4';
        $cRow->save();

        $row = $model->getRow(1);
        $rows = $row->getChildRows('Child');
        $this->assertEquals(count($rows), 4);

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
        $this->assertEquals('bar2', $row->getChildRows('Child', $select)
                                                            ->current()
                                                            ->bar);
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

    public function testCreateChildRowForNewUnsavedRow()
    {
        $cModel = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_ChildModel');
        $model = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_Model');
        $row = $model->createRow();
        $cRow = $row->createChildRow('Child');
        $row->save();
        $this->assertEquals(3, $row->id);
        $this->assertEquals(3, $cRow->id);
        $this->assertEquals(3, $cRow->test_id);
        $this->assertEquals(3, $model->getRow(3)->id);
        $this->assertTrue(!!$cModel->getRow(3));
        $this->assertEquals(3, $cModel->getRow(3)->test_id);
    }

    public function testChildRowsAreAutomagicallySavedWithParentRow()
    {
        $cModel = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_ChildModel');
        $model = Vps_Model_Abstract::getInstance('Vps_Model_ChildRows_Model');
        $row = $model->getRow(1);
        $cRow = $row->getChildRows('Child')->current();
        $this->assertEquals(1, $cRow->id);
        $cRow->foo = 'blub';
        $row->save();
        $this->assertEquals('blub', $cModel->getRow(1)->foo);
    }
}
