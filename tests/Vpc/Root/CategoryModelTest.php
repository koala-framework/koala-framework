<?php
/**
 * @group CategoryModel
 */
class Vpc_Root_CategoryModelTest extends Vps_Test_TestCase
{
    public function testModel()
    {
        $config = array(
            'pageCategories' => array('main' => 'Hauptmenü', 'bottom' => 'Unten')
        );
        $model = new Vpc_Root_CategoryModel($config);
        $this->assertEquals($model->getRow('main')->id, 'main');
        $this->assertEquals($model->getRow('bottom')->name, 'Unten');
        $this->assertEquals($model->countRows(), 2);
    }
}
