<?php
class Vps_Model_FnF_ModelTest extends PHPUnit_Framework_TestCase
{
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
}
