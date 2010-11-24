<?php
/**
 * @group Update_Action
 */
class Vps_Update_Action_Db_RenameFieldTest extends Vps_Test_TestCase
{
    public function testRenameField()
    {
        $model = new Vps_Update_Action_Db_TestModel();

        $this->assertNotNull($model->getRow('foo'));

        $a = new Vps_Update_Action_Db_DropTable();
        $a->model = $model;
        $a->table = 'foo';
        $a->silent = true;
        $a->update();

        $this->assertNull($model->getRow('foo'));
    }
}
