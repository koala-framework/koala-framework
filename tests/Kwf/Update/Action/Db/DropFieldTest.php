<?php
/**
 * @group Update_Action
 */
class Kwf_Update_Action_Db_DropFieldTest extends Kwf_Test_TestCase
{
    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass(false);
    }

    public function testRenameField()
    {
        $model = new Kwf_Update_Action_Db_TestModel();

        $rows = $model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(2, count($rows));

        $a = new Kwf_Update_Action_Db_DropField();
        $a->model = $model;
        $a->table = 'foo';
        $a->field = 'bar';
        $a->silent = true;
        $a->update();

        $rows = $model->getRow('foo')->getChildRows('Fields');
        $this->assertEquals(1, count($rows));

        $row = $model->getRow('foo')->getChildRows('Fields', $model->select()->whereId('bar'))->current();
        $this->assertNull($row);
    }
}
