<?php
/**
 * @group Update_Action
 */
class Vps_Update_Action_Db_ChangeFieldTest extends PHPUnit_Framework_TestCase
{
    public function testChangeField()
    {
        $model = new Vps_Update_Action_Db_TestModel();

        $rows = $model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $a = new Vps_Update_Action_Db_ChangeField();
        $a->model = $model;
        $a->table = 'foo';
        $a->field = 'bar';
        $a->type = 'int';
        $a->default = 5;
        $a->update();

        $rows = $a->model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $rows = $a->model->getRow('foo')->getChildRows('Fields',
                $a->model->select()->whereId('bar'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('bar', $row->field);
        $this->assertEquals('int', $row->type);
        $this->assertEquals(5, $row->default);
    }
}
