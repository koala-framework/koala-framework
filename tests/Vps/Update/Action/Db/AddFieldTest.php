<?php
/**
 * @group Update_Action
 */
class Vps_Update_Action_Db_AddFieldTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass(false);
    }

    public function testAddField()
    {
        $model = new Vps_Update_Action_Db_TestModel();

        $rows = $model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $a = new Vps_Update_Action_Db_AddField();
        $a->model = $model;
        $a->table = 'foo';
        $a->field = 'new_field';
        $a->type = 'text';
        $a->silent = true;
        $a->update();

        $rows = $a->model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(3, count($rows));

        $rows = $a->model->getRow('foo')->getChildRows('Fields',
                $a->model->select()->whereId('new_field'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('new_field', $row->field);
        $this->assertEquals('text', $row->type);
    }
}
