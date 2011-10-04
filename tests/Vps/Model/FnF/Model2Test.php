<?php
/**
 * @group Model
 * @group Model_FnF
 */
class Vps_Model_FnF_Model2Test extends Vps_Test_TestCase
{
    public function testData()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));
        $this->assertEquals(count($model->getRows()), 2);
        $this->assertEquals(count($model->getRows(1)), 1);
        $this->assertEquals($model->getRow(2)->value, 'bar');
        $this->assertEquals($model->getRows()->current()->value, 'foo');
        $this->assertEquals($model->getRow(3), null);
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
        $this->assertEquals(count($model->getRows()), 4);

        $select = $model->select();
        $select->whereEquals('value', 'foo');
        $this->_assertIds($model, $select, array(1));

        $select = $model->select();
        $select->whereEquals('value', array('foo', 'baz'));
        $this->_assertIds($model, $select, array(1, 3));
        $this->assertEquals($model->countRows($select), 2);
    }

    public function testWhereNotEquals()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
            array('id' => 3, 'value' => 'baz'),
            array('id' => 4, 'value' => 'buz'),
        ));
/*
        $select = $model->select();
        $select->whereNotEquals('value', 'foo');
        $this->_assertIds($model, $select, array(2, 3, 4));
*/
        $select = $model->select();
        $select->whereNotEquals('value', array('foo', 'baz'));
        $this->_assertIds($model, $select, array(2, 4));
//         $this->assertEquals($model->countRows($select), 2);
    }
    private function _assertIds($model, $select, $ids)
    {
        $res = array();
        foreach ($model->getRows($select) as $r) {
            $res[] = $r->id;
        }
        $this->assertEquals($ids, $res);
    }
}
