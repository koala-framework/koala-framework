<?php
/**
 * @group Update_Action
 */
class Kwf_Update_Action_Db_RenameFieldTest extends Kwf_Test_TestCase
{
    public function setUp()
    {
        Kwf_Component_Data_Root::setComponentClass(false);
    }

    public function testRenameField()
    {
        $model = new Kwf_Update_Action_Db_TestModel();

        $this->assertNotNull($model->getRow('foo'));

        $a = new Kwf_Update_Action_Db_DropTable();
        $a->model = $model;
        $a->table = 'foo';
        $a->silent = true;
        $a->update();

        $this->assertNull($model->getRow('foo'));
    }
}
