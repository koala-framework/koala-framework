<?php
/**
 * @group CategoryModel
 */
class Kwc_Root_CategoryModelTest extends Kwf_Test_TestCase
{
    public function testModel()
    {
        $config = array(
            'pageCategories' => array('main' => 'Hauptmenü', 'bottom' => 'Unten')
        );
        $model = new Kwc_Root_CategoryModel($config);
        $this->assertEquals($model->getRow('main')->id, 'main');
        $this->assertEquals($model->getRow('bottom')->name, 'Unten');
        $this->assertEquals($model->countRows(), 2);
    }
}
