<?php
/**
 * @group Model
 * @group Model_FnF
 */
class Vps_Model_FnF_ModelTest extends PHPUnit_Framework_TestCase
{
    public function testRowUnset()
    {
        $fnf = new Vps_Model_FnF(array(
            'data' => array(
                array('id' => 4, 'names' => 'foo')
            ),
            'columns' => array('id', 'names')
        ));

        $row = $fnf->getRow(4);
        unset($row->names);
    }

    public function testData()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));
        $this->assertEquals(count($model->fetchAll()), 2);
        $this->assertEquals(count($model->find(1)), 1);
        $this->assertEquals($model->find(2)->current()->value, 'bar');
        $this->assertEquals($model->fetchAll()->current()->value, 'foo');
        $this->assertEquals(count($model->find(3)), 0);
    }

    public function testDeleteRows()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
            ));
        $model->deleteRows($model->select()->whereEquals('value', 'foo'));
        $this->assertEquals(count($model->fetchAll()), 1);
        $this->assertEquals(count($model->find(1)), 0);
        $this->assertEquals($model->find(2)->current()->value, 'bar');
        $model->deleteRows($model->select());
        $this->assertEquals(count($model->fetchAll()), 0);
    }

    public function testSelect()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
            array('id' => 3, 'value' => 'baz'),
            array('id' => 4, 'value' => 'buz'),
        ));
        $this->assertEquals(count($model->fetchAll()), 4);

        $select = $model->select();
        $select->whereEquals('value', 'foo');
        $this->_assertIds($model, $select, array(1));

        $select = $model->select();
        $select->whereEquals('value', array('foo', 'baz'));
        $this->_assertIds($model, $select, array(1, 3));
        $this->assertEquals($model->fetchCount($select), 2);

        $select = $model->select();
        $select->order('value');
        $select->whereEquals('value', array('foo', 'baz'));
        $this->_assertIds($model, $select, array(3, 1));

        $select = $model->select();
        $select->order('value', 'DESC');
        $select->whereEquals('value', array('foo', 'baz'));
        $this->_assertIds($model, $select, array(1, 3));
    }

    public function testWhereId()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
            array('id' => 3, 'value' => 'baz'),
            array('id' => 4, 'value' => 'buz'),
        ));
        $select = $model->select();
        $select->whereId(1);
        $this->_assertIds($model, $select, array(1));
    }

    private function _assertIds($model, $select, $ids)
    {
        $res = array();
        foreach ($model->fetchAll($select) as $r) {
            $res[] = $r->id;
        }
        $this->assertEquals($ids, $res);
    }

    public function testLimit()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
            array('id' => 3, 'value' => 'baz'),
            array('id' => 4, 'value' => 'buz'),
        ));
        $select = $model->select();
        $select->limit(1);
        $this->_assertIds($model, $select, array(1));

        $select = $model->select();
        $select->limit(2);
        $this->_assertIds($model, $select, array(1, 2));
    }

    public function testOrderRand()
    {
        $model = new Vps_Model_FnF();
        $data = array();
        for ($i=0;$i<100;$i++) {
            $data[] = array('id'=>$i+1);
        }
        $model->setData($data);
        $select = $model->select()
            ->order(Vps_Model_Select::ORDER_RAND);
        $ids1 = array();
        foreach ($model->fetchAll($select) as $row) {
            $ids1[] = $row->id;
        }
        $ids2 = array();
        foreach ($model->fetchAll($select) as $row) {
            $ids2[] = $row->id;
        }
        $this->assertTrue($ids1 != $ids2);
    }

    public function testSave()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'foo'=>'')
        )));
        $row = $model->getRow(1);
        $row->foo = 'bar';
        $row->save();

        $this->assertEquals($model->getData(), array(array('id'=>1, 'foo'=>'bar')));

        $row = $model->getRow(1);
        $this->assertEquals($row->foo, 'bar');
    }

    public function testDoubleCreateRow()
    {
        $this->setExpectedException('Vps_Exception');
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=> 1, 'foo'=>'')
        )));
        $row = $model->createRow(array('foo' => 'hallo'));
        $row->save();

        $rowNew = $model->createRow(array('id' => 2, 'foo' => 'hallo'));
        $rowNew->save();
    }

    public function testDelete()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'foo'=>'')
        )));
        $row = $model->getRow(1);
        $row->delete();

        $this->assertEquals($model->getData(), array());
    }

    public function testInsertAutoId()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'foo'=>'')
        )));
        $row = $model->createRow();
        $row->foo = 'bar2';
        $row->save();

        $this->assertEquals($model->getData(), array(
            array('id'=>1, 'foo'=>''),
            array('id'=>2, 'foo'=>'bar2')
        ));
    }
    public function testChangeId()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'foo'=>'')
        )));
        $row = $model->getRow(1);
        $row->id = 2;
        $row->save();

        $this->assertEquals($model->getData(), array(
            array('id'=>2, 'foo'=>'')
        ));
    }
    public function testInsertManualId()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'foo'=>'')
        )));
        $row = $model->createRow();
        $row->id = 10;
        $row->foo = 'bar2';
        $row->save();

        $this->assertEquals($model->getData(), array(
            array('id'=>1, 'foo'=>''),
            array('id'=>10, 'foo'=>'bar2')
        ));
    }

    public function testIsEqual()
    {
        $fnf1 = new Vps_Model_FnF();
        $fnf2 = new Vps_Model_FnF();
        $this->assertTrue($fnf1->isEqual($fnf1));
        $this->assertFalse($fnf1->isEqual($fnf2));
        $this->assertFalse($fnf2->isEqual($fnf1));
    }

    public function testUniqueRowObject()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'foo'=>'')
        )));
        $r1 = $model->getRow(1);
        $r2 = $model->getRow(1);
        $this->assertEquals($r2->foo, '');
        $r1->foo = 'foo';
        $this->assertEquals($r2->foo, 'foo');
        $this->assertTrue($r1 === $r2);

        $r3 = $model->getRows()->current();
        $this->assertTrue($r1 === $r3);
    }
    public function testUniqueRowObjectCreateRow()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'name' => 'foo'),
        ));

        $r1 = $model->createRow();
        $newId = $r1->save();
        $this->assertEquals(2, $newId);

        $r2 = $model->getRow(2);
        $this->assertTrue($r1 === $r2);
    }

    public function testUniqueRowObjectDelete()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'name' => 'foo1'),
            array('id' => 2, 'name' => 'foo2'),
            array('id' => 3, 'name' => 'foo3'),
        ));

        $r = $model->getRow(3);

        $this->assertTrue($r === $model->getRow(3));
        $model->getRow(2)->delete();
        $this->assertTrue($r === $model->getRow(3));
    }

    public function testUniqueRowObjectDeleteCreateRow()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'name' => 'foo1'),
            array('id' => 2, 'name' => 'foo2'),
        ));

        $model->getRow(1);
        $model->getRow(2);

        $model->getRow(2)->delete();

        $r1 = $model->createRow();
        $newId = $r1->save();
        $this->assertEquals(2, $newId);

        $model->getRow(1)->delete();

        $this->assertTrue($r1 === $model->getRow(2));
    }

    public function testDefaultValues()
    {
        $model = new Vps_Model_FnF(array(
            'default' => array('foo'=>'defaultFoo')
        ));
        $row = $model->createRow();
        $this->assertEquals('defaultFoo', $row->foo);
    }

    public function testNonNumericIds()
    {
        $model = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>'1'),
                array('id'=>'3-foo'),
                array('id'=>4),
            )
        ));

        $this->assertNotNull($model->getRow(1));
        $this->assertNotNull($model->getRow('1'));
        $this->assertNull($model->getRow('3'));
        $this->assertNull($model->getRow(3));
        $this->assertNull($model->getRow($model->select()->whereId(3)));
        $this->assertNull($model->getRow($model->select()->whereId('3')));
        $this->assertEquals(0, count($model->getRows($model->select()->whereId('3'))));
        $this->assertEquals(0, count($model->getRows($model->select()->whereId(3))));
        $this->assertEquals(0, count($model->getRows($model->select()->whereEquals('id', 3))));
        $this->assertEquals(0, count($model->getRows($model->select()->whereEquals('id', '3'))));
        $this->assertNotNull($model->getRow('3-foo'));
        $this->assertNull($model->getRow('4-foo'));
        $this->assertNull($model->getRow($model->select()->whereId('4-foo')));
        $this->assertEquals(0, count($model->getRows($model->select()->whereId('4-foo'))));
        $this->assertEquals(0, count($model->getRows($model->select()->whereEquals('id', '4-foo'))));
    }

    public function testImportReplace()
    {
        $model = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>'1', 'xy' => 'blub'),
                array('id'=>'3-foo', 'xy' => 'blab'),
                array('id'=>'4', 'xy' => 'bleb'),
            ),
            'uniqueColumns' => array('id')
        ));
        $data = array(array('id'=>'1', 'xy'=>'blub'), array('id'=>'2'), array('id'=>'4', 'xy'=>'NEW'));
        $model->import(Vps_Model_Interface::FORMAT_ARRAY, $data, array('replace'=>true));
        $this->assertEquals(4, count($model->getRows()));
        $this->assertEquals('NEW', $model->getRow(4)->xy);
    }

    public function testDuplicateRow()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));
        $row = $model->getRow(2)->duplicate();
        $row->save();

        $this->assertEquals(3, $row->id);
        $this->assertEquals('bar', $row->value);

        // duplicate a duplicated row
        $row = $row->duplicate();

        $this->assertEquals(4, $row->id);
        $this->assertEquals('bar', $row->value);

        // duplicate a not yet saved, duplicated row
        $rowFirst = $model->getRow(1)->duplicate(array('value' => 'bla'));
        $rowSecond = $rowFirst->duplicate();

        $this->assertEquals(5, $rowFirst->id);
        $this->assertEquals('bla', $rowFirst->value);

        $this->assertEquals(6, $rowSecond->id);
        $this->assertEquals('bla', $rowSecond->value);
    }

    public function testDontSaveNotDirtyRow()
    {
        $model = $this->getMock('Vps_Model_FnF', array('update'));
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));

        $model->expects($this->never())
            ->method('update');
        $row = $model->getRow(2);
        $ret = $row->save();
        $this->assertEquals(2, $ret);
    }

    public function testSaveNewRowNotDirty()
    {
        $model = $this->getMock('Vps_Model_FnF', array('insert'));
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));

        $model->expects($this->once())
            ->method('insert');

        $row = $model->createRow();
        $row->save();
    }

    public function testSaveDirtyRow()
    {
        $model = $this->getMock('Vps_Model_FnF', array('update', 'insert'));
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));

        $model->expects($this->once())
            ->method('update');
        $model->expects($this->once())
            ->method('insert');

        $row = $model->getRow(2);
        $row->value = 'blubb';
        $row->save();

        $row = $model->createRow();
        $row->id = 3;
        $row->value = 'bloe';
        $row->save();
    }

    public function testDirtyColumns()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));

        $row = $model->getRow(1);
        $this->assertEquals($row->getDirtyColumns(), array());
        $this->assertEquals($row->isDirty(), false);
        $row->value = 'blubb';
        $this->assertEquals($row->getDirtyColumns(), array('value'));
        $this->assertEquals($row->isDirty(), true);
    }
}
