<?php
/**
 * @group Update_Action
 */
class Vps_Update_Action_Db_DropTableTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass(false);
    }

    public function testDropTable()
    {
        $model = new Vps_Update_Action_Db_TestModel();

        $rows = $model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $a = new Vps_Update_Action_Db_RenameField();
        $a->model = $model;
        $a->table = 'foo';
        $a->field = 'bar';
        $a->newName = 'new_bar';
        $a->silent = true;
        $a->update();

        $rows = $a->model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $rows = $a->model->getRow('foo')->getChildRows('Fields',
                $a->model->select()->whereId('new_bar'));
        $this->assertEquals(1, count($rows));
        $row = $rows->current();
        $this->assertEquals('new_bar', $row->field);
        $this->assertEquals('text', $row->type);
    }
}
